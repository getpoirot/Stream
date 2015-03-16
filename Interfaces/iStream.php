<?php
namespace Poirot\Stream\Interfaces;

interface iStream
{
    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string $socketUri Socket Uri
     */
    function __construct($socketUri);

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
    function withNoneBlocking($flag = true);

    /**
     * Indicate Where Stream Is Built With None-Blocking Mode?
     *
     * @return boolean
     */
    function isNoneBlocking();

    /**
     * Get Current Socket Uri That Stream Built With
     *
     * @return string
     */
    function getSocketUri();
}
