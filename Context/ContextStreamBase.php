<?php
namespace Poirot\Stream\Context;

class ContextStreamBase 
    extends aContextStream
{
    /**
     * Construct
     *
     * @param string                  $wrapperName   Context wrapper name
     * @param null|array|\Traversable $contextParams Options
     */
    function __construct($wrapperName, $contextParams = null)
    {
        $this->wrapper = (string) $wrapperName;
        parent::__construct($contextParams);
    }
}
