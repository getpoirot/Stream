<?php
namespace Poirot\Stream\Context;

use Poirot\Stream\Interfaces\Wrapper\iSWrapper;

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
     * @param string|iSWrapper|array $wrapper
     * @param array                  $options
     */
    function __construct($wrapper = null, $options = [])
    {
        if ($wrapper instanceof iSWrapper) {
            $this->wrapper = $wrapper->getLabel();
            $this->options()->from($wrapper->options());
        } elseif(is_string($wrapper))
            $this->wrapper = $wrapper;
        else
            $options = $wrapper;

        parent::__construct($options);
    }
}
