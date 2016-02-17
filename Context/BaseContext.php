<?php
namespace Poirot\Stream\Context;

use Poirot\Stream\Interfaces\Wrapper\ipSWrapper;

/*
fopen($label.'://stream'
    , (string) $mode
    , null
    ## set options to wrapper
    , (new BaseContext($label, ['stream' => $stream]))->toContext()
);

// instead of:
    , stream_context_create([
        $label => ['stream' => $stream]
    ])
*/

class BaseContext extends AbstractContext
{
    /**
     * Construct
     *
     * [code]
     * new BaseContext('label', [..options])
     * // options inside wrapper will merge with extra options
     * new BaseContext(iSWrapper, [..extraOptions])
     * [/code]
     *
     * @param string|ipSWrapper|array $wrapper
     * @param array                  $options
     */
    function __construct($wrapper = null, $options = [])
    {
        if ($wrapper instanceof ipSWrapper) {
            $this->wrapper = $wrapper->getLabel();
            $this->inOptions()->from($wrapper->inOptions());
        } elseif(is_string($wrapper))
            $this->wrapper = $wrapper;
        else
            $options = $wrapper;

        parent::__construct($options);
    }
}
