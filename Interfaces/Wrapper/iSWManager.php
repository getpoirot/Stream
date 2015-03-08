<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWManager 
{
    /**
     * Register Stream Wrapper
     *
     * - If Not Set Using iSWrapper
     *
     * @param iSWrapper $wrapper
     * @param null      $label   Wrapper Label
     *
     * @return
     */
    static function register(iSWrapper $wrapper, $label = null);

    /**
     * @link http://php.net/manual/en/function.stream-wrapper-unregister.php
     *
     * UnRegister Wrapper
     *
     * @param string|iSWrapper $label
     */
    static function unregister($label);

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string $wrapper
     *
     * @return boolean
     */
    static function hasRegister($wrapper);

    /**
     * @link http://php.net/manual/en/function.stream-get-wrappers.php
     *
     * Get List Of Registered Wrappers
     *
     * @return [string]
     */
    static function listRegWrappers();
}
