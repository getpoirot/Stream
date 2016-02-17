<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWManager
{
    /**
     * Register Stream Wrapper
     *
     * @param ipSWrapper $wrapper
     * @param null      $label   Wrapper Label
     *        - If Not Set Using iSWrapper
     *
     * @throw \Exception If Wrapper Registered Before
     */
    static function register(ipSWrapper $wrapper, $label = null);

    /**
     * UnRegister Wrapper
     *
     * @param string|ipSWrapper $label
     */
    static function unregister($label);

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|ipSWrapper $wrapper
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
