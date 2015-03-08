<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWrapper
{
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
    function getLabel();
}
