<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Core\AbstractOptions;
use Poirot\Core\OpenOptions;
use Poirot\Stream\Interfaces\Wrapper\iSWrapper;

/*
// How to inject options into wrapper as default context:
// ++
fopen('label://stream', 'r', null
    ## set options to wrapper
    , stream_context_create([
        ## ------- this is options [->getStream()]
        'label' => ['stream' => $stream]
    ])
);
*/

abstract class AbstractWrapper
    implements
    iSWrapper
{
    /**
     * Context Wrapper Options
     *
     * The current context, or NULL if no context was passed to the caller function.
     * Use the stream_context_get_options() to parse the context
     *
     * @var resource
     */
    public $context;

    /**
     * @var AbstractOptions|OpenOptions
     */
    protected $options;

    /**
     * Get Wrapper Protocol Label
     *
     * - used on register/unregister wrappers, ...
     *
     *   label://
     *   -----
     *
     * @return string
     */
    abstract function getLabel();

    /**
     * @return AbstractOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = self::optionsIns();

        if ($this->context) {
            ## set options from injected context
            $contextOpt = stream_context_get_options($this->context);
            $contextOpt = $contextOpt[$this->getLabel()];

            $this->options->from($contextOpt);
        }

        return $this->options;
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
     * @return AbstractOptions
     */
    static function optionsIns()
    {
        return new OpenOptions;
    }
}
 