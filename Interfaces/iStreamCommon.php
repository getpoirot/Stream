<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Context\iContextStream;

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
    function getServerAddress();

    /**
     * Context Options
     *
     * @param iContextStream $context
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function setContext(iContextStream $context);

    /**
     * Get Context Options
     *
     * @return iContextStream
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
     * - must store time in float mode
     *   @see self::getTimeout
     *
     * @param float|array $seconds In Form Of time.utime
     *
     * @return $this
     */
    function setTimeout($seconds);

    /**
     * Get Timeout
     *
     * @return float
     */
    function getTimeout();
}
