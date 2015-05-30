<?php
namespace Poirot\Stream\Context\Http;

use Poirot\Core\AbstractOptions;
use Poirot\Stream\Context\AbstractContext;
use Poirot\Stream\Context\Socket\SocketContext;

/**
 * @method SocketContext socket()
 */
class HttpContext extends AbstractContext
{
    protected $wrapper = 'http';

    protected function __before_construct()
    {
        // Bind Socket Context
        $this->bindContext(new SocketContext);
    }

    /**
     * Set/Retrieves specific options
     *
     * ! Implement Just for ide auto complete
     *   on @!return object
     *
     * @return SCHttpOptions
     */
    function options()
    {
        return parent::options();
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
     * @return SCHttpOptions
     */
    static function optionsIns()
    {
        return new SCHttpOptions;
    }
}
