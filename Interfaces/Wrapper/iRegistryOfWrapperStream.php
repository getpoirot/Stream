<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iRegistryOfWrapperStream
{
    /**
     * Register Stream Wrapper
     *
     * @param iWrapperStream $wrapper
     * @param null      $label   Wrapper Label
     *        - If Not Set Using iSWrapper
     *
     * @throw \Exception If Wrapper Registered Before
     */
    static function register(iWrapperStream $wrapper, $label = null);

    /**
     * UnRegister Wrapper
     *
     * @param string|iWrapperStream $label
     */
    static function unregister($label);

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|iWrapperStream $wrapper
     *
     * @return boolean
     */
    static function isRegistered($wrapper);

    /**
     * Get List Of Registered Wrappers
     *
     * @return array[string]
     */
    static function listWrappers();
}
