<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Std\Struct\aDataOptions;
use Poirot\Std\Struct\DataOptionsOpen;

use Poirot\Stream\Interfaces\Wrapper\iWrapperStream;

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

abstract class aWrapperStream
    implements
    iWrapperStream
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
     * @var aDataOptions|DataOptionsOpen
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
     * @return DataOptionsOpen
     */
    function optsData()
    {
        if (!$this->options)
            $this->options = self::newOptsData();

        if ($this->context) {
            ## set options from injected context
            $contextOpt = stream_context_get_options($this->context);
            $contextOpt = $contextOpt[$this->getLabel()];

            $this->options->import($contextOpt);
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
     * @return DataOptionsOpen
     */
    static function newOptsData($builder = null)
    {
        return new DataOptionsOpen($builder);
    }
}
