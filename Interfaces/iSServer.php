<?php
namespace Poirot\Stream\Interfaces;

/**
 * Creates a stream or datagram socket on the specified
 * Local Socket
 *
 * related:
 * - Retrieve list of registered socket transports
 * @link http://php.net/manual/en/function.stream-get-transports.php
 *
 * @link http://php.net/manual/en/function.stream-socket-server.php#44501
 *
 */
interface iSServer
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
