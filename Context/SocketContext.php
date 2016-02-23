<?php
namespace Poirot\Stream\Context;

/**
 * Socket context options are available for all wrappers
 * that work over sockets, like tcp, http and ftp
 *
 * $opts = array(
 *  'socket' => array(
 *      // connect to the internet using the '192.168.0.100' IP
 *      'bindto' => '192.168.0.100:0',
 *
 *      // connect to the internet using the '192.168.0.100' IP and port '7000'
 *      'bindto' => '192.168.0.100:7000',
 *
 *      // connect to the internet using the '2001:db8::1' IPv6 address and port '7000'
 *      'bindto' => '[2001:db8::1]:7000',
 *
 *     // connect to the internet using port '7000'
 *     'bindto' => '0:7000',
 *  ),
 */
class SocketContext extends AbstractContext
{
    protected $wrapper = 'socket';

    // Options
    protected $bindto;
    protected $backlog;


    /**
     * Used to specify the IP address (either IPv4 or IPv6) and/or the
     * port number that PHP will use to access the network.
     *
     * Note: As FTP creates two socket connections during normal operation,
     * the port number cannot be specified using this option
     *
     * @param string $addr Address Of Host:port
     *
     * @return $this
     */
    function setBindto($addr)
    {
        $this->bindto = $addr;
        return $this;
    }

    /**
     * @return string
     */
    function getBindto()
    {
        return $this->bindto;
    }

    /**
     * Used to limit the number of outstanding connections in
     * the socket's listen queue
     *
     * @param int $num
     *
     * @return $this
     */
    function setBacklog($num)
    {
        $this->backlog = $num;
        return $this;
    }

    /**
     * @return int
     */
    function getBacklog()
    {
        return $this->backlog;
    }
}
