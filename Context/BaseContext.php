<?php
namespace Poirot\Stream\Context;

use Poirot\Std\Interfaces\Struct\iOptionsData;

class BaseContext extends AbstractContext
{
    /**
     * Construct
     *
     * @param string             $wrapperName Context wrapper name
     * @param array|iOptionsData $options     Options
     */
    function __construct($wrapperName, $options = null)
    {
        $this->wrapper = (string) $wrapperName;
        parent::__construct($options);
    }
}
