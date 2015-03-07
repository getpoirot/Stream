<?php
namespace Poirot\Stream\Interfaces;

/**
 * @link http://php.net/manual/en/function.stream-socket-sendto.php
 * @link http://php.net/manual/en/function.stream-socket-client.php
 */
interface iStreamClient 
{
    /**
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
     * @param string $localSocket
     *
     * @return $this
     */
    function setTransport($localSocket);

    function setContext();

    /**
     * Bitmask field which may be set to any combination of
     * connection flags. Currently the select of connection
     * flags is limited to STREAM_CLIENT_CONNECT (default),
     * STREAM_CLIENT_ASYNC_CONNECT and STREAM_CLIENT_PERSISTENT.
     *
     * @return $this
     */
    function setFlag();

    /**
     * @link http://php.net/manual/en/function.stream-socket-client.php
     *
     * @return iStreamResource
     */
    function getConnect();
}