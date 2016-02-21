<?php
namespace Poirot\Stream\Context\Socket;

use Poirot\Stream\Context\AbstractContext;

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

    /**
     * Set/Retrieves specific options
     *
     * ! Implement Just for ide auto complete
     *   on @!return object
     *
     * @return SCSocketOptions
     */
    function optsData()
    {
        return parent::optsData();
    }

    /**
     * Get An Bare Options Instance
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @param null|mixed $builder Builder Options as Constructor
     *
     * @return SCSocketOptions
     */
    static function newOptsData($builder = null)
    {
        return new SCSocketOptions;
    }
}
