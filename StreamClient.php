<?php
namespace Poirot\Stream;

use Poirot\Stream\Context\Socket\SocketContext;
use Poirot\Stream\Interfaces\Context\iSContext;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamClient;

class StreamClient implements iStreamClient
{
    /**
     * @var string
     */
    protected $socketUri;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * @var boolean
     */
    protected $persistent;

    /**
     * @var boolean
     */
    protected $noneBlocking;

    /**
     * @var iSContext
     */
    protected $context;

    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * TODO: socketUri Can converted to an pathUri Object
     *
     * @param string                         $socketUri Socket Uri
     * @param iSContext|array|resource| null $context   Context Options
     */
    function __construct($socketUri, $context = null)
    {
        $this->setSocketUri($socketUri);

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
        if (!in_array($scheme, stream_get_transports()))
            throw new \Exception(sprintf(
                'Transport "%s" not supported.'
                , $scheme
            ));

        $resource = $this->__connect_transport($sockUri);

        return new SResource($resource);
    }

    /**
     * @link http://php.net/manual/en/function.stream-socket-client.php
     * @link http://php.net/manual/en/function.fsockopen.php
     * @link http://php.net/manual/en/function.pfsockopen.php
     */
    protected function __connect_transport($sockUri)
    {
        // timeout:
        $timeout = $this->getTimeout();

        // persistence
        $flags = ($this->isPersistent())
            ? STREAM_CLIENT_PERSISTENT
            : STREAM_CLIENT_CONNECT;

        // get connect to resource:
        $errstr = $errno = null;
        $resource = @stream_socket_client(
            $sockUri
            , $errno
            , $errstr
            , $timeout
            , $flags
            , $this->getContext()->toContext()
        );
        if (!$resource)
            throw new \Exception($errstr, $errno);

        // none blocking mode:
        if ($this->isNoneBlocking())
            // it will work after connection has made on resource
            stream_set_blocking($resource, 0); // 0 for none-blocking

        return $resource;
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
     * Set To Persistent Internet or Unix Domain Socket
     * Connection Built
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setPersistent($flag = true)
    {
        $this->persistent = (boolean) $flag;

        return $this;
    }

    /**
     * Indicate Is Connection Have To Built On Persistent Mode
     *
     * @return boolean
     */
    function isPersistent()
    {
        return $this->persistent;
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
}
