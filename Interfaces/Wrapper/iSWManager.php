<?php
namespace Poirot\Stream\Interfaces\Wrapper;

interface iSWManager
{
    /**
     * @link http://php.net/manual/en/function.stream-wrapper-register.php
     *
     * Register Stream Wrapper
     *
     * @param iSWrapper $wrapper
     * @param null      $label   Wrapper Label
     *                           - If Not Set Using iSWrapper
     *
     * @throw \Exception If Wrapper Registered Before
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
    static function isRegistered($wrapper);

    /**
     * @link http://php.net/manual/en/function.stream-get-wrappers.php
     *
     * Get List Of Registered Wrappers
     *
     * @return [string]
     */
    static function listRegWrappers();
}
