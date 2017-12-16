<?php
namespace Poirot\Stream;

use Poirot\Std\ErrorStack;
use Poirot\Std\ConfigurableSetter;
use Poirot\Std\Mixin;

use Poirot\Stream\Context\ContextStreamSocket;
use Poirot\Stream\Interfaces\Context\iContextStream;
use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\iStreamClient;

/*

$socket = new StreamClient([
    'socket_uri'    => 'tcp://google.com:80',
    'time_out'      => 30,
    'persistent'    => true,
    'none_blocking' => true,
    'on_resource'   => ['dump_debug' => function($resource) {kd($resource);}],
]);

$conn   = $socket->getConnect();
$stream = new Streamable($conn);

$stream->write((string) $request);

// ======================================================

$socket = new StreamClient([
    'socket_uri'    => 'tcp://google.com:80',
    'time_out'      => 30,
]);

$conn   = $socket->getConnect();
$stream = new Streamable($conn);

$request = (new HttpRequest(['method' => 'GET', 'host' => 'localhost', 'headers' => [
    'Accept' => ' * /*',
    'User-Agent' => 'Poirot/Client HTTP',
]]))->toString();

$stream->write($request);
$response = $stream->read();
$response = new HttpResponse($response);
$response->getPluginManager()->set(new Status());
if ($response->plugin()->status()->isSuccess())
    echo $response->getBody();

*/

class StreamClient
    extends ConfigurableSetter
    implements iStreamClient
{
    /** @var string */
    protected $socketUri    = null;

    // default options

    /** @var float */
    protected $timeout      = 20;
    /** @var boolean */
    protected $persist      = false;
    /** @var boolean */
    protected $noneBlocking = false;
    /** @var boolean */
    protected $async        = false;
    /** @var iContextStream */
    protected $context;
    
    // Events ....
    /** @var Mixin */
    protected $_on__resource_connected;

    /** @var iResourceStream */
    protected $_c__connectedResource;
    
    
    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string|array                       $serverAddressOrSetter Socket Uri
     * @param iContextStream                     $context   Context Options
     */
    function __construct($serverAddressOrSetter, $context = null)
    {
        $setters = array();

        if (\Poirot\Std\isStringify($serverAddressOrSetter)) {
            ## maybe using some stringify like pathuri as input
            $setters['server_address'] = (string) $serverAddressOrSetter;

            if ($context !== null)
                $setters['context'] = $context;
        }

        parent::__construct($setters);
    }


    // ...
    
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
        if (!in_array($scheme, stream_get_transports()))
            throw new \Exception(sprintf(
                'Transport "%s" not supported.'
                , $scheme
            ));

        $resource = $this->_connect_transport($sockUri);
        $resource = new ResourceStream($resource);

        if (!$this->isPersist())
            ## close opened connections
            $this->_c__connectedResource[] = $resource;

        return $resource;
    }

    /**
     * Add Closure Callable On Connected Resource
     *
     * - the closure functions will bind to this object
     *
     * closure:
     * function($resource, $self) {
     *   // $this available for php 5.4
     * }
     *
     *
     * @return Mixin
     */
    function onResourceAvailable()
    {
        if (!$this->_on__resource_connected)
            $this->_on__resource_connected = new Mixin($this);

        return $this->_on__resource_connected;
    }
    
    // Options:

    /**
     * Proxy to StreamClient::whenResourceAvailable for setupFromArray
     *
     * ['on_resource' =>
     *   ['method_name' => to callable with args ($resource, $self) ],
     *   ['method_name', function($resource, $self) ],
     * ]
     * 
     * @param $methods
     */
    protected function setOnResource(array $methods)
    {
        if ( count($methods) <= 2
            && array_filter($methods, function($item) { return is_callable($item); }) 
        )
            ##! 'on_resource' => ['dump_debug' => function($resource) {k($resource);}]
            $methods = array($methods);

        foreach($methods as $method) {
            $name = null;
            if (is_array($method)) {
                if (count($method) === 2) {
                    ##! ['method_name', \Closure]
                    $name = $method[0]; $fn = $method[1];
                } elseif (array_values($method) !== $method) {
                    ##! ['method_name' => \Closure]
                    $name = key($method);
                    $fn = current($method);
                }
            }

            if ($name === null)
                throw new \InvalidArgumentException(
                    'Unknown Method Type Provided For '.\Poirot\Std\flatten($method)
                );

            $this->onResourceAvailable()->addMethod($name, $fn);
        }
    }
    
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
        $this->socketUri = $socketUri;
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
     * Set Default Base Context Options
     *
     * @param iContextStream $context
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
     * Get Default Base Context Options
     *
     * @return iContextStream
     */
    function getContext()
    {
        if (!$this->context)
            $this->setContext(new ContextStreamSocket());

        return $this->context;
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
     * Set To Persistent Internet or Unix Domain Socket
     * Connection Built
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setPersist($flag = true)
    {
        $this->persist = (boolean) $flag;
        return $this;
    }

    /**
     * Indicate Is Connection Have To Built On Persistent Mode
     *
     * @return boolean
     */
    function isPersist()
    {
        return $this->persist;
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
     * @param boolean $async
     * @return $this
     */
    function setAsync($async = true)
    {
        $this->async = (boolean) $async;
        return $this;
    }

    /**
     * @return boolean
     */
    function isAsync()
    {
        return $this->async;
    }

    
    // ..

    /**
     * @link http://php.net/manual/en/function.stream-socket-client.php
     * @link http://php.net/manual/en/function.fsockopen.php
     * @link http://php.net/manual/en/function.pfsockopen.php
     */
    protected function _connect_transport($sockUri)
    {
        // timeout:
        $timeout = $this->getTimeout();

        // persistence
        $flags = STREAM_CLIENT_CONNECT;
        (!$this->isPersist()) ?: $flags |= STREAM_CLIENT_PERSISTENT;
        // asynchronous
        (!$this->isAsync())   ?: $flags |= STREAM_CLIENT_ASYNC_CONNECT;


        // get connect to resource:
        $errstr = $errno = null;

        $context = $this->getContext();

        ErrorStack::handleError(E_ALL); // -------------------------------------------\
        $resource = stream_socket_client(
            $sockUri
            , $errno
            , $errstr
            , $timeout
            , $flags
            , $context->toContext()
        );

        // Fire up registered methods on resource
        // It may used to add extra options or context that not available inside this class.
        foreach($this->onResourceAvailable()->listMethods() as $method)
            #! Mixin in php 5.4 and above are support bind to otherwise it will
            #- pass the bindto as object on last arguments of methods
            #- So, we pass the object $this as last argument to support both scenarion
            call_user_func(array($this->onResourceAvailable(), $method), $resource, $this);

        $error = ErrorStack::handleDone();
        if ($error) {
            throw new \Exception(sprintf(
                'Cannot Connect To Server "%s".'
                , $this->getServerAddress()
            ), $errno, $error);
        }

        // ---------------------------------------------------------------------------/

        # Set the stream timeout
        $timeOut = explode('.', (string) $this->getTimeout());
        (isset($timeOut[1])) ?: $timeOut[1] = null;
        @stream_set_timeout($resource, $timeOut[0], $timeOut[1]);
        
        # None blocking mode:
        if ($this->isNoneBlocking())
            // it will work after connection has made on resource
            stream_set_blocking($resource, 0); // 0 for none-blocking

        return $resource;
    }


    // ...

    function __destruct()
    {
        if ($this->_c__connectedResource)
            foreach($this->_c__connectedResource as $cn) {
            /** @var ResourceStream $cn */
                ## close connection if not persist
                ErrorStack::handleError();
                $cn->close();
                ErrorStack::handleDone();
            }
    }
}
