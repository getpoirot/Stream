<?php
namespace Poirot\Stream\Context;

use Poirot\Core\AbstractOptions;
use Poirot\Stream\Context\Http\SCHttpOptions;
use Poirot\Stream\Context\Socket\SCSocketOptions;

class HttpContext extends AbstractContext
{
    protected $wrapper = 'http';

    /**
     * @var SCSocketOptions
     */
    protected $socket;

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
     * @return AbstractOptions
     */
    static function optionsIns()
    {
        return new SCHttpOptions;
    }

    /**
     * Socket Options
     *
     * @return SCSocketOptions
     */
    function socket()
    {
        if (!$this->socket)
            $this->socket = new SCSocketOptions;

        return $this->socket;
    }
}
 