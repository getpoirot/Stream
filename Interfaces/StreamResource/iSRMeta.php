<?php
namespace Poirot\Stream\Interfaces\StreamResource;

/**
 * @link http://php.net/manual/en/function.stream-get-meta-data.php
 */
interface iSRMeta
{
    /**
     * Construct
     *
     * @param resource $stream
     */
    function __construct($stream);

    /**
     * The URI/filename Associated With This Stream
     *
     * @return string
     */
    function getUri();

    /**
     * The Number Of Bytes Currently Contained In The
     * PHP's Own Internal Buffer
     *
     * @return int
     */
    function getUnreadBytes();

    /**
     * A Label Describing The Underlying Implementation
     * Of The Stream
     *
     * @return string
     */
    function getStreamType();

    /**
     * A Label Describing The Protocol Wrapper Implementation
     * Layered Over The Stream
     *
     * @return string
     */
    function getWrapperType();

    /**
     * Wrapper Specific Data Attached To This Stream
     *
     * @return mixed
     */
    function getWrapperData();

    /**
     * The Type Of Access Required For This Stream
     *
     * @return string
     */
    function getAccessType();

    /**
     * Is Stream Timed Out While Waiting For
     * Data On The Last Call?
     *
     * @return boolean
     */
    function isTimedOut();

    /**
     * Whether The Current Stream Can Be seeked?
     *
     * @return boolean
     */
    function isSeekable();

    /**
     * Is The Stream In None-Blocking IO Mode?
     *
     * @return boolean
     */
    function isNoneBlocking();

    /**
     * Is The Stream Has Reached End-Of-File?
     *
     * ! this can be TRUE even when unread_bytes is non-zero
     *
     * @return boolean
     */
    function isReachedEnd();
}
