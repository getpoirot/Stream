<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Context\iSContext;

interface iStreamCommon
{
    /**
     * Construct
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * TODO: socketUri Can converted to an pathUri Object
     *
     * @param string                         $socketUri Socket Uri
     * @param iSContext|array|resource| null $context   Context Options
     */
    function __construct($socketUri, $context = null);

    /**
     * Context Options
     *
     * @return iSContext
     */
    function context();

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
     * TODO: Socket Uri Can converted to an pathUri Object
     *
     * @return string
     */
    function getSocketUri();
}
