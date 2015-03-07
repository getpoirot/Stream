<?php
namespace Poirot\Stream\Interfaces\Context;

use Poirot\Core\Interfaces\iPoirotOptions;

interface iCBaseOptions extends iPoirotOptions
{
    /**
     * Used to specify the IP address (either IPv4 or IPv6)
     * and/or the port number that PHP will use to access
     * the network.
     *
     * ! The syntax is ip:port for IPv4 addresses, and [ip]:port for IPv6 addresses
     * ! Setting the IP or the port to 0 will let the system choose the IP and/or port
     *
     * @param string $bind
     *
     * @return $this
     */
    function setBindto($bind);

    /**
     * Get Binding Specify IP Address
     *
     * @return string
     */
    function getBindto();
}
