<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Context\iSContext;

interface iStreamCommon
{
    /**
     * Get Current Socket Uri That Stream Built With
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @return string
     */
    function getSocketUri();

    /**
     * Context Options
     *
     * @param iSContext|array|resource $context
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function setContext($context);

    /**
     * Get Context Options
     *
     * @return iSContext
     */
    function getContext();

    /**
     * Set blocking/non-blocking mode on a stream
     *
     * ! This function works for any stream that supports
     *   non-blocking mode (currently, regular files and socket streams)
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setNoneBlocking($flag = true);

    /**
     * Indicate Where Stream Is Built With None-Blocking Mode?
     *
     * @return boolean
     */
    function isNoneBlocking();

    /**
     * Set timeout period on a stream
     *
     * @see iSResource::setTimeout
     *
     * @param float $seconds In Form Of 5.3
     *
     * @return $this
     */
    function setTimeout($seconds);

    /**
     * Get Timeout
     *
     * @return array[$second, $microsecond]
     */
    function getTimeout();
}
