<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWManager
{
    /**
     * Register Stream Wrapper
     *
     * @param iSWrapper $wrapper
     * @param null      $label   Wrapper Label
     *        - If Not Set Using iSWrapper
     *
     * @throw \Exception If Wrapper Registered Before
     */
    static function register(iSWrapper $wrapper, $label = null);

    /**
     * UnRegister Wrapper
     *
     * @param string|iSWrapper $label
     */
    static function unregister($label);

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|iSWrapper $wrapper
     *
     * @return boolean
     */
    static function isRegistered($wrapper);

    /**
     * Get List Of Registered Wrappers
     *
     * @return array[string]
     */
    static function listRegWrappers();
}
