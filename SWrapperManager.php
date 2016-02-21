<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\Wrapper\iSWManager;
use Poirot\Stream\Interfaces\Wrapper\ipSWrapper;

class SWrapperManager implements iSWManager
{
    /**
     * Register Stream Wrapper
     *
     * @param ipSWrapper $wrapper
     * @param null      $label   Wrapper Label
     *        - If Not Set Using iSWrapper
     *
     * @throws \Exception If Wrapper Registered Before
     */
    static function register(ipSWrapper $wrapper, $label = null)
    {
        if ($label == null)
            $label = $wrapper->getLabel();

        if ($pos = strpos($label, ':') !== false)
            throw new \Exception(sprintf(
                '(%s) Is Invalid Label Provided For Stream Wrapper.'
                . ' It must include just a name like "http".'
                , $label
            ));

        stream_wrapper_register($label, get_class($wrapper));

        // Set the default stream context which will be used whenever
        // file operations (fopen(), file_get_contents(), etc...) are
        // called without a context parameter.
        $options = [
            $label => \Poirot\Std\iterator_to_array($wrapper->optsData())
        ];

        stream_context_set_default($options);
    }

    /**
     * UnRegister Wrapper
     *
     * @param string|ipSWrapper $label
     */
    static function unregister($label)
    {
        if ($label instanceof ipSWrapper)
            $label = $label->getLabel();

        stream_wrapper_unregister($label);
    }

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|ipSWrapper $wrapper
     *
     * @return boolean
     */
    static function isRegistered($wrapper)
    {
        if ($wrapper instanceof ipSWrapper)
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
