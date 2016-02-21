<?php
namespace Poirot\Stream\Context\Socket;

use Poirot\Std\Struct\OpenOptionsData;

class SCSocketOptions extends OpenOptionsData
{
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
 