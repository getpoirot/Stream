<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Stream\Interfaces\Wrapper\iRegistryOfWrapperStream;
use Poirot\Stream\Interfaces\Wrapper\iWrapperStream;

class RegistryOfWrapperStream 
    implements iRegistryOfWrapperStream
{
    /**
     * Register Stream Wrapper
     *
     * @param iWrapperStream $wrapper
     * @param null           $label   Wrapper Label
     *                                - If Not Set Using iSWrapper::getLabel
     *
     * @throws \Exception If Wrapper Registered Before
     */
    static function register(iWrapperStream $wrapper, $label = null)
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
        $options = array(
            $label => \Poirot\Std\cast($wrapper->optsData())->toArray()
        );

        stream_context_set_default($options);
    }

    /**
     * UnRegister Wrapper
     *
     * @param string|iWrapperStream $label
     */
    static function unregister($label)
    {
        if ($label instanceof iWrapperStream)
            $label = $label->getLabel();

        stream_wrapper_unregister($label);
    }

    /**
     * Has Registered Wrapper With Name?
     *
     * @param string|iWrapperStream $wrapper
     *
     * @return boolean
     */
    static function isRegistered($wrapper)
    {
        if ($wrapper instanceof iWrapperStream)
            $wrapper = $wrapper->getLabel();

        return in_array($wrapper, self::listWrappers());
    }

    /**
     * Get List Of Registered Wrappers
     *
     * @return string[]
     */
    static function listWrappers()
    {
        return stream_get_wrappers();
    }
}
