<?php
namespace Poirot\Stream;

use Poirot\Stream\Context\Socket\SocketContext;
use Poirot\Stream\Interfaces\Context\iSContext;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iWrapperClient;
use Poirot\Stream\Interfaces\Resource\iSRAccessMode;
use Poirot\Stream\Resource\SROpenMode;

class WrapperClient implements iWrapperClient
{
    /**
     * @var string
     */
    protected $socketUri;

    /**
     * @var iSContext
     */
    protected $context;

    /**
     * @var array
     */
    protected $timeout;

    /**
     * @var SROpenMode
     */
    protected $openmode;

    /**
     * @var boolean
     */
    protected $noneBlocking;

    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * TODO: socketUri Can converted to an pathUri Object
     *
     * @param string                        $socketUri Socket Uri
     * @param iSRAccessMode|string          $openMode  iSRAccessMode::MODE_*
     * @param iSContext|array|resource|null $context   Context Options
     */
    function __construct($socketUri, $openMode = iSRAccessMode::MODE_RB, $context = null)
    {
        $this->setSocketUri($socketUri);

        if ($openMode === null)
            $openMode = iSRAccessMode::MODE_RB;

        if ($openMode instanceof iSRAccessMode)
            $this->setOpenmode($openMode);

        if (is_string($openMode))
            $this->getOpenmode()->fromString($openMode);

        if ($context !== null) {
            if ($context instanceof iSContext)
                $this->setContext($context);
            else
                $this->getContext()->from($context);
        }
    }

    /**
     * Set Socket Uri
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * TODO: socketUri Can converted to an pathUri Object
     *
     * @param string $socketUri
     *
     * @return $this
     */
    function setSocketUri($socketUri)
    {
        $this->socketUri = $socketUri;

        return $this;
    }

    /**
     * Get Current Socket Uri That Stream Built With
     *
     * TODO: Socket Uri Can converted to an pathUri Object
     *
     * @return string
     */
    function getSocketUri()
    {
        return $this->socketUri;
    }

    /**
     * Open Socket Connection To Socket Uri
     *
     * - Initiates a stream or datagram connection to the
     *   destination specified by socketUri.
     *   The type of socket created is determined by the
     *   transport specified using standard URL formatting:
     *   transport://target
     *
     *   ! For Internet Domain sockets (AF_INET) such as
     *     TCP and UDP, the target portion of the socketUri
     *     parameter should consist of a hostname or IP address
     *     followed by a colon and a port number.
     *     For Unix domain sockets, the target portion should
     *     point to the socket file on the filesystem
     *
     * Note: The stream will by default be opened in blocking mode.
     *
     * Warning UDP sockets will sometimes appear to have opened without
     * an error, even if the remote host is unreachable. The error will
     * only become apparent when you read or write data to/from the socket.
     * The reason for this is because UDP is a "connectionless" protocol,
     * which means that the operating system does not try to establish a
     * link for the socket until it actually needs to send or receive data
     *
     * @throws \Exception On Connection Failed
     * @return iSResource
     */
    function getConnect()
    {
        $sockUri = $this->getSocketUri();

        // knowing transport/wrapper:
        $scheme  = parse_url($sockUri, PHP_URL_SCHEME);
        if (!SWrapperManager::isRegistered($scheme))
            throw new \Exception(sprintf(
                'Wrapper "%s" not supported.'
                , $scheme
            ));

        $resource = $this->__connect_wrapper($sockUri);
        return new SResource($resource);
    }

    /**
     * @link http://php.net/manual/en/function.fopen.php
     */
    protected function __connect_wrapper($sockUri)
    {
        $resource = fopen($sockUri
            , $this->getOpenmode()->toString()
            , null
            , $this->getContext()->toContext()
        );

        if (!$resource)
            throw new \Exception('Error Connecting to '.$sockUri);

        // set timeout:
        $timeOut = explode('.', $this->getTimeout());
        (isset($timeOut[1])) ?: $timeOut[1] = null;
        @stream_set_timeout($resource, $timeOut[0], $timeOut[1]);

        // none blocking mode:
        if ($this->isNoneBlocking())
            // it will work after connection has made on resource
            @stream_set_blocking($resource, 0); // 0 for none-blocking

        return $resource;
    }

    /**
     * Context Options
     *
     * @param iSContext $context
     *
     * @return $this
     */
    function setContext(iSContext $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get Context Options
     *
     * @return iSContext
     */
    function getContext()
    {
        if (!$this->context)
            $this->setContext(new SocketContext);

        return $this->context;
    }

    /**
     * Set blocking/non-blocking mode on a stream
     *
     * ! This function works for any stream that supports
     *   non-blocking mode (currently, regular files and socket streams)
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setNoneBlocking($flag = true)
    {
        $this->noneBlocking = (boolean) $flag;

        return $this;
    }

    /**
     * Indicate Where Stream Is Built With None-Blocking Mode?
     *
     * @return boolean
     */
    function isNoneBlocking()
    {
        return $this->noneBlocking;
    }

    /**
     * Set timeout period on a stream
     *
     * @see iSResource::setTimeout
     *
     * @param float $seconds In Form Of 5.3
     *
     * @return $this
     */
    function setTimeout($seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Get Timeout
     *
     * @return array[$second, $microsecond]
     */
    function getTimeout()
    {
        if (!$this->timeout)
            $this->timeout = ini_get('default_socket_timeout');

        return $this->timeout;
    }

    /**
     * Open Wrapper R/W Mode
     *
     * @param iSRAccessMode $mode
     *
     * @return SROpenMode
     */
    function setOpenmode(iSRAccessMode $mode)
    {
        $this->openmode = $mode;

        return $this;
    }

    /**
     * Get Open Access Mode
     *
     * @return SROpenMode
     */
    function getOpenmode()
    {
        if (!$this->openmode)
            $this->setOpenmode(new SROpenMode);

        return $this->openmode;
    }
}
 
