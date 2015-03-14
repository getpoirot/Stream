<?php
namespace Poirot\Stream\Interfaces;

/**
 * Creates a stream or datagram socket on the specified
 * Local Socket
 */
interface iSocketServer
{
    /**
     * Construct
     *
     * The type of socket created is determined by the
     * transport specified using standard URL formatting:
     * transport://target
     *
     * - For Internet Domain sockets (AF_INET) such as
     *   TCP and UDP, the target portion of the remote_socket
     *   parameter should consist of a hostname or IP address
     *   followed by a colon and a port number
     *
     * - For Unix domain sockets, the target portion should
     *   point to the socket file on the filesystem.
     *
     * List of Supported Socket Transports:
     * @link http://php.net/manual/en/transports.php
     *
     * Note: For UDP sockets, you must use STREAM_SERVER_BIND as
     *       the flags parameter.
     *
     * Note: Most systems require root access to create a server
     *       socket on a port below 1024.
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets for example,
     *       tcp://[fe80::1]:80
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
    function bind($port = 0);

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
