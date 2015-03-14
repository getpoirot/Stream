<?php
namespace Poirot\Stream\Interfaces\Context;

use Poirot\Core\Interfaces\iOptionImplement;
use Poirot\Core\Interfaces\iPoirotOptions;

/**
 * Note: Don't include wrapper type for toArray
 *       fromArray result,
 *       such as: $arr['wrapper']['option'] = $value
 *
 */
interface iSContext extends iOptionImplement
{
    /**
     * Used To Create Context, as php on creating streams
     * get contexts options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @param string $wrapper
     *
     * @return $this
     */
    function forWrapper($wrapper);

    /**
     * Set/Retrieves parameters
     *
     * - data params used on $this::toContext
     *   to set params of context
     *
     * @return iPoirotOptions
     */
    function params();

    /**
     * Set Options From Context Resource
     *
     * - get parameters from context and store on object
     *   by $this::params
     * - rewrite wrapper with resource wrapper name
     *
     * @param resource $resource Context/Stream
     * @return $this
     */
    function fromContext($resource);

    /**
     * Creates and returns a stream context with any
     * options supplied in options preset
     *
     * - Set Parameters On Context
     *   parameters are accessible by $this::params
     *   method.
     *
     * @throws \Exception not wrapper defined
     * @return resource
     */
    function toContext();
}
