<?php
namespace Poirot\Stream\Interfaces\Context;

use Poirot\Std\Interfaces\iOptionImplement;
use Poirot\Std\Interfaces\iPoirotOptions;

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
     * contexts get options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @throws \Exception Wrapper not defined
     * @return string
     */
    function wrapperName();

    /**
     * Bind Another Context Along this
     *
     * [
     *   'socket' => [
     *     'bindto' => '192.168.0.100:7000',
     *   ],
     *   'http' => [
     *      ...
     *   ]
     * ]
     *
     * @param iSContext|array|resource $context
     *
     * @return $this
     */
    function bindWith($context);

    /**
     * Context with specific wrapper has bind?
     *
     * @param string $wrapperName
     *
     * @return false|iSContext
     */
    function hasBind($wrapperName);

    /**
     * List of Wrapper Name Of Currently Bind Contexts
     *
     * @return array[ (string) wrapperName ]
     */
    function listBindContexts();

    /**
     * Set/Retrieves specific socket options
     *
     * - data params used on $this::toContext
     *   to set params of context
     *
     * @return iPoirotOptions
     */
    function inOptions();

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
    function fromResource($resource);

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
