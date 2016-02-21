<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Std\Struct\AbstractOptionsData;
use Poirot\Std\Struct\OpenOptionsData;
use Poirot\Stream\Interfaces\Wrapper\ipSWrapper;

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
    ipSWrapper
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
     * @var AbstractOptionsData|OpenOptionsData
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
     * @return AbstractOptionsData
     */
    function optsData()
    {
        if (!$this->options)
            $this->options = self::newOptsData();

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
     *  @param null|mixed $builder Builder Options as Constructor
     *
     * @return AbstractOptionsData
     */
    static function newOptsData($builder = null)
    {
        return new OpenOptionsData($builder);
    }
}
