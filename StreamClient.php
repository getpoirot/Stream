<?php
namespace Poirot\Stream;

use Poirot\Std\BuilderSetterTrait;
use Poirot\Std\ErrorStack;
use Poirot\Std\OpenCall;
use Poirot\Stream\Interfaces\Context\iSContext;
use Poirot\Stream\Interfaces\iSResource;
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

class StreamClient implements iStreamClient
{
    use BuilderSetterTrait;
    use StreamClientOptionsTrait;

    /** @var iSResource */
    protected $_c__connectedResource;

    // Events ....
    /** @var OpenCall */
    protected $_on__resource_connected;

    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string|array                   $socketUri Socket Uri or Array of Builder Settings
     * @param iSContext|array|resource|null  $context   Context Options
     */
    function __construct($socketUri = null, $context = null)
    {
        if (is_array($socketUri) && !empty($socketUri))
            $this->setupFromArray($socketUri);
        elseif (is_string($socketUri))
            $this->setSocketUri($socketUri);
        elseif ($socketUri !== null)
            throw new \InvalidArgumentException(sprintf(
                'StreamClient Construct give string or array of settings builder as first argument. given "%s".'
                , \Poirot\Std\flatten($socketUri)
            ));

        if ($context !== null) {
            if ($context instanceof iSContext)
                $this->setContext($context);
            else
                $this->getContext()->from($context);
        }
    }


    // ...

    /**
     * Add Closure Callable On Connected Resource
     *
     * - the closure functions will bind to this object
     *
     * closure:
     * function($resource) {
     *   // $this will point to StreamClient(current)
     * }
     *
     *
     * @return OpenCall
     */
    function onResourceAvailable()
    {
        if (!$this->_on__resource_connected)
            $this->_on__resource_connected = new OpenCall($this);

        return $this->_on__resource_connected;
    }

    /**
     * Proxy to StreamClient::whenResourceAvailable for setupFromArray
     *
     * ['on_resource' =>
     *   ['method_name' => \Closure],
     *   ['method_name', \Closure],
     * ]
     *
     * @param $methods
     */
    protected function setOnResource(array $methods)
    {
        if ( count($methods) <= 2 && array_filter($methods, function($item) { return is_callable($item); }) )
            ##! 'on_resource' => ['dump_debug' => function($resource) {k($resource);}]
            $methods = [$methods];

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
                throw new \InvalidArgumentException('Unknown Method Type Provided For '.\Poirot\Std\flatten($method));

            $this->onResourceAvailable()->addMethod($name, $fn);
        }
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
        $resource = new SResource($resource);

        if (!$this->isPersist())
            ## close opened connections
            $this->_c__connectedResource[] = $resource;

        return $resource;
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
        $flags = STREAM_CLIENT_CONNECT;
        (!$this->isPersist()) ?: $flags |= STREAM_CLIENT_PERSISTENT;
        // asynchronous
        (!$this->isAsync()) ?: $flags |= STREAM_CLIENT_ASYNC_CONNECT;


        // get connect to resource:
        $errstr = $errno = null;

        ErrorStack::handleError(E_ALL); // -------------------------------------------\
        $resource = stream_socket_client(
            $sockUri
            , $errno
            , $errstr
            , $timeout
            , $flags
            , $this->getContext()->toContext()
        );

        // Fire up registered methods on resource
        foreach($this->onResourceAvailable()->listMethods() as $method)
            call_user_func([$this->onResourceAvailable(), $method], $resource);

        $error = ErrorStack::handleDone();
        if ($error)
            throw new \Exception(sprintf(
                'Cannot Connect To Server "%s".'
                , $this->getSocketUri()
            ), $errno, $error);
        // ----------------------------------------------------------------------------

        // Set the stream timeout
        if (!stream_set_timeout($resource, (int) $this->getTimeout()))
            throw new \RuntimeException('Unable to set the connection timeout');

        // none blocking mode:
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
                ## close connection if not persist
                ErrorStack::handleError();
                $cn->close();
                ErrorStack::handleDone();
            }
    }
}
