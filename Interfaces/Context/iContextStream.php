<?php
namespace Poirot\Stream\Interfaces\Context;

use Poirot\Std\Interfaces\Struct\iDataOptions;

interface iContextStream 
    extends iDataOptions
{
    /**
     * Used To Create Context, as php on creating streams
     * contexts get options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @throws \Exception Wrapper not defined
     * @return string
     */
    function getWrapperName();

    /**
     * Bind Another Context Along this
     *
     * [
     *   'socket' => [
     *     // socket context options
     *     ...
     *   ],
     *   'http' => [
     *     // http context options
     *     ...
     *   ]
     * ]
     *
     * @param iContextStream $context
     *
     * @return $this
     */
    function bindWith(iContextStream $context);

    /**
     * Context with specific wrapper has bind?
     *
     * @param string $wrapperName
     *
     * @return false|iContextStream
     */
    function hasBind($wrapperName);

    /**
     * List of Wrapper Name Of Currently Bind Contexts
     *
     * @return array[ (string) wrapperName ]
     */
    function listBindContexts();

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
