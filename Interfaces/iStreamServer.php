<?php
namespace Poirot\Stream\Interfaces;

/**
 * Creates a stream or datagram socket on the specified
 * Local Socket
 *
 */
interface iStreamServer extends iStreamCommon
{
    /**
     * Open Socket Connection To Socket Uri and Bind Server
     * Socket To Specific Port
     *
     * - Initiates a stream or datagram connection to the
     *   destination specified by socketUri.
     *   The type of socket created is determined by the
     *   transport specified using standard URL formatting:
     *   transport://target
     *
     * - store socket server resource inside class
     * - each time bind was calling the resource
     *   created again
     *
     * ! Port eq to zero let system to select unused port
     * ! Most systems require root access to create
     *   a server socket on a port below 1024
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
     * Note: For UDP sockets, you must use STREAM_SERVER_BIND as
     *       the flags parameter.
     *
     * Note: Most systems require root access to create a server
     *       socket on a port below 1024.
     *
     * Warning UDP sockets will sometimes appear to have opened without
     * an error, even if the remote host is unreachable. The error will
     * only become apparent when you read or write data to/from the socket.
     * The reason for this is because UDP is a "connectionless" protocol,
     * which means that the operating system does not try to establish a
     * link for the socket until it actually needs to send or receive data
     *
     * @throws \Exception On Connection Failed
     * @return $this
     */
    function bind();

    /**
     * Is Server Binding On Socket?
     *
     * @return boolean
     */
    function isBinding();

    /**
     * Listen On Port To Accept Data On That Port
     * From Client
     *
     * Warning with UDP server sockets. use stream_socket_recvfrom()
     * and stream_socket_sendto().
     *
     * @throws \Exception
     * @return iStreamable
     */
    function listen();
}
