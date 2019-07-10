<?php
namespace Poirot\Stream;

use Poirot\Std\ConfigurableSetter;

use Poirot\Stream\Exception\ConnectionError;
use Poirot\Stream\Interfaces\Context\iContextStream;
use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\iWrapperStreamClient;
use Poirot\Stream\Interfaces\Resource\iAccessModeToResourceStream;
use Poirot\Stream\Context\ContextStreamSocket;
use Poirot\Stream\Resource\AccessMode;
use Poirot\Stream\Wrapper\RegistryOfWrapperStream;

class StreamWrapperClient
    extends ConfigurableSetter
    implements iWrapperStreamClient
{
    const DEFAULT_ACCESS_MODE = iAccessModeToResourceStream::MODE_RB;

    /** @var string */
    protected $socketUri;

    /** @var iContextStream */
    protected $context;

    /** @var array */
    protected $timeout;

    /** @var AccessMode */
    protected $accessMode;

    /** @var boolean */
    protected $noneBlocking;


    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string|array                       $serverAddressOrSetter Socket Uri
     * @param iAccessModeToResourceStream|string $accMode   iSRAccessMode::MODE_*
     * @param iContextStream                     $context   Context Options
     */
    function __construct(
        $serverAddressOrSetter
        , $accMode = null
        , $context = null
    ) {
        $setters = array();

        if (\Poirot\Std\isStringify($serverAddressOrSetter)) {
            ## maybe using some stringify like pathuri as input
            $setters['server_address'] = (string) $serverAddressOrSetter;
            
            if ($accMode !== null)
                $setters['access_mode'] = $accMode;

            if ($context !== null)
                $setters['context'] = $context;
        }

        parent::__construct($setters);
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
     * @return iResourceStream
     */
    function getConnect()
    {
        $sockUri = $this->getServerAddress();

        // knowing transport/wrapper:
        $scheme  = parse_url($sockUri, PHP_URL_SCHEME);
        if (!$scheme)
            ## /path/to/file.ext
            return $this->setServerAddress("file://{$sockUri}")
                ->getConnect();


        if (!RegistryOfWrapperStream::isRegistered($scheme))
            throw new \Exception(sprintf(
                'Wrapper (%s) not supported.'
                , $scheme
            ));

        $resource = $this->_connect_wrapper($sockUri);
        return new ResourceStream($resource);
    }


    // Options:

    /**
     * Set Socket Uri
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string $socketUri
     *
     * @return $this
     */
    function setServerAddress($socketUri)
    {
        $this->socketUri = (string) $socketUri;
        return $this;
    }

    /**
     * Get Current Socket Uri That Stream Built With
     *
     * @return string
     */
    function getServerAddress()
    {
        return $this->socketUri;
    }

    /**
     * Context Options
     *
     * @param iContextStream|array|resource $context
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function setContext(iContextStream $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get Context Options
     *
     * @return iContextStream
     */
    function getContext()
    {
        if (!$this->context)
            $this->setContext(new ContextStreamSocket);

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
     * - must store time in float mode
     *   @see self::getTimeout
     *
     * @param float|array $seconds In Form Of time.utime
     *
     * @return $this
     */
    function setTimeout($seconds)
    {
        if (is_array($seconds))
            $seconds = implode('.', $seconds);

        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Get Timeout
     *
     * @return float
     */
    function getTimeout()
    {
        if (!$this->timeout)
            $this->setTimeout(ini_get('default_socket_timeout'));

        return $this->timeout;
    }

    /**
     * Open Wrapper R/W Mode
     *
     * @param iAccessModeToResourceStream|string $mode
     *
     * @return $this
     */
    function setAccessMode($mode)
    {
        $this->getAccessMode()->fromString((string) $mode);
        return $this;
    }

    /**
     * Get Open Access Mode
     *
     * @return AccessMode
     */
    function getAccessMode()
    {
        if (!$this->accessMode)
            $this->accessMode = new AccessMode(self::DEFAULT_ACCESS_MODE);

        return $this->accessMode;
    }


    // ..

    /**
     * @link http://php.net/manual/en/function.fopen.php
     */
    protected function _connect_wrapper($sockUri)
    {
        $resource = fopen(
            $sockUri
            , $this->getAccessMode()->toString()
            , null
            , $this->getContext()->toContext()
        );

        if (false === $resource)
            throw new ConnectionError('Error Connecting to ' . $sockUri);

        // set timeout:
        $timeOut = explode('.', (string) $this->getTimeout());
        (isset($timeOut[1])) ?: $timeOut[1] = null;
        @stream_set_timeout($resource, $timeOut[0], $timeOut[1]);

        // none blocking mode:
        if ($this->isNoneBlocking())
            // it will work after connection has made on resource
            @stream_set_blocking($resource, 0); // 0 for none-blocking

        return $resource;
    }
}
