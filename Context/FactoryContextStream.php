<?php
namespace Poirot\Stream\Context;

use Poirot\Std\Interfaces\Pact\ipFactory;
use Poirot\Stream\Interfaces\Context\iContextStream;

class FactoryContextStream
    implements ipFactory
{
    /**
     * Factory ContextOption From context resource
     *
     * - rewrite wrapper with resource wrapper name
     *
     * @param resource $resource Context/Stream
     * 
     * @return iContextStream
     */
    static function of($resource)
    {
        if (!is_resource($resource) && get_resource_type($resource) !== 'stream-context')
            throw new \InvalidArgumentException(sprintf(
                'Invalid Context Resource Passed, given: "%s".'
                , \Poirot\Std\flatten($resource)
            ));


        // ..
        $options = stream_context_get_params($resource);
        $context = new ContextStreamSocket($options); // Socket Context can be used as Base Context
        return $context;
    }
}
