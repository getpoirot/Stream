<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\Wrapper\iSWManager;
use Poirot\Stream\Interfaces\Wrapper\iSWrapper;

class SWrapperManager implements iSWManager
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
    static function register(iSWrapper $wrapper, $label = null)
    {
        if ($label == null)
            $label = $wrapper->getLabel();

        stream_wrapper_register($label, get_class($wrapper));

        // Set the default stream context which will be used whenever
        // file operations (fopen(), file_get_contents(), etc...) are
        // called without a context parameter.
        $options = [
            $label => $wrapper->options()->toArray()
        ];

        stream_context_set_default($options);
    }

    /**
     * UnRegister Wrapper
     *
     * @param string|iSWrapper $label
     */
    static function unregister($label)
    {
        if ($label instanceof iSWrapper)
            $label = $label->getLabel();

        stream_wrapper_unregister($label);
    }

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|iSWrapper $wrapper
     *
     * @return boolean
     */
    static function isRegistered($wrapper)
    {
        if ($wrapper instanceof iSWrapper)
            $wrapper = $wrapper->getLabel();

        return in_array($wrapper, self::listRegWrappers());
    }

    /**
     * Get List Of Registered Wrappers
     *
     * @return array[string]
     */
    static function listRegWrappers()
    {
        return stream_get_wrappers();
    }
}
 