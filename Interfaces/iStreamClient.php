<?php
namespace Poirot\Stream\Interfaces;

/**
 * @link http://php.net/manual/en/function.stream-socket-client.php
 * @link http://php.net/manual/en/function.fsockopen.php
 * @link http://php.net/manual/en/function.pfsockopen.php
 */
interface iStreamClient extends iStream
{
    /**
     * Set timeout period on a stream
     *
     * @see iSResource::setTimeout
     *
     * @param int $seconds      The seconds part of the timeout to be set
     * @param int $microseconds The microseconds part of the timeout to be set
     *
     * @return $this
     */
    function withTimeout($seconds, $microseconds);

    /**
     * Set To Persistent Internet or Unix Domain Socket
     * Connection Built
     *
     * @param bool $flag
     *
     * @return $this
     */
    function withPersistent($flag = true);

    /**
     * Indicate Is Connection Have To Built On Persistent Mode
     *
     * @return boolean
     */
    function isPersistent();

    /**
     * Get Timeout
     *
     * @return array['second' => int, 'microsecond' => int]
     */
    function getTimeout();
}
