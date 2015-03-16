<?php
namespace Poirot\Stream\Interfaces;

/**
 * Creates a stream or datagram socket on the specified
 * Local Socket
 *
 * @link http://php.net/manual/en/function.stream-socket-server.php
 */
interface iStreamServer extends iStream
{
    /**
     * Construct
     *
     * Note: For UDP sockets, you must use STREAM_SERVER_BIND as
     *       the flags parameter.
     *
     * Note: Most systems require root access to create a server
     *       socket on a port below 1024.
     *
     * @param string $uriLocalSocket Uri To Local Socket
     *
     * @throws \Exception If Can't Connect To Server
     */
    function __construct($uriLocalSocket);
    /*
     * resource stream_socket_server (
     *  string $local_socket
     *  [, int &$errno
     *  [, string &$errstr
     *  [, int $flags = STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
     *  [, resource $context ]]]]
     * )
     */

    /**
     * @link http://php.net/manual/en/function.stream-socket-server.php
     *
     * Bind Server Socket To Specific Port
     *
     * - store socket server resource inside class
     * - each time bind was calling the resource
     *   created again
     *
     * ! Port eq to zero let system to select unused port
     * ! Most systems require root access to create
     *   a server socket on a port below 1024
     *
     * @param int $port
     *
     * @throw \Exception
     * @return $this
     */
    function bind($port = -1);

    /**
     * @link http://php.net/manual/en/function.stream-socket-accept.php
     * @link http://php.net/manual/en/function.stream-socket-accept.php#47088
     * @link http://php.net/manual/en/function.stream-socket-recvfrom.php
     *
     * Listen On Port To Accept Data On That Port
     * From Client
     *
     * @return iSResource
     */
    function listen();
}
