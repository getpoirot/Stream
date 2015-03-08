<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWrapper
{
    /**
     * Get Wrapper Label
     *
     * - used on register/unregister wrappers, ...
     *
     * @return string
     */
    function getLabel();
}
