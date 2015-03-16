<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Filter\iSFilter;
use Poirot\Stream\Interfaces\Resource\iSResMetaReader;

interface iSResource
{
    /**
     * Get Resource Origin Handler
     *
     * - check for resource to be available
     *
     * @throws \Exception On Closed/Not Available Resource
     * @return resource
     */
    function getRHandler();

    /**
     * Retrieve the name of the local sockets
     *
     * @return string
     */
    function getLocalName();

    /**
     * Retrieve the name of the remote sockets
     *
     * ! in tcp connections it will return ip address of
     *   remote server (64.233.185.106:80)
     *
     * @return string
     */
    function getRemoteName();

    /**
     * Meta Data About Handler
     *
     * @return iSResMetaReader
     */
    function meta();

    /**
     * @link http://php.net/manual/en/function.stream-set-timeout.php
     *
     * Set timeout period on a stream
     *
     * ! When the stream times out,
     *   the 'timed_out' key of the array returned
     *   by stream_get_meta_data() is set to TRUE,
     *   although no error/warning is generated.
     *
     * Note: This parameter only applies when not making asynchronous
     *       connection attempts
     *
     * Note: To set a timeout for reading/writing data over the socket,
     *       use the stream_set_timeout(), as the timeout only applies
     *       while making connecting the socket
     *
     * Note: This function doesn't work with advanced operations like
     *       stream_socket_recvfrom(), use stream_select() with timeout
     *       parameter instead
     *
     * @param int $seconds      The seconds part of the timeout to be set
     * @param int $microseconds The microseconds part of the timeout to be set
     *
     * @return $this
     */
    function setTimeout($seconds, $microseconds);

    /**
     * Append Filter
     *
     * [code]
     *  $filter->appendTo($this)
     * [/code]
     *
     * @param iSFilter $filter
     * @param int      $rwFlag  @see iSFilter::AppendTo
     *
     * @return $this
     */
    function appendFilter(iSFilter $filter, $rwFlag = STREAM_FILTER_ALL);

    /**
     * Attach a filter to a stream
     *
     * @param iSFilter $filter
     * @param int      $rwFlag
     *
     * @return $this
     */
    function prependFilter(iSFilter $filter, $rwFlag = STREAM_FILTER_ALL);

    /**
     * Remove Given Filter From Resource
     *
     * @param iSFilter $filter
     *
     * @return $this
     */
    function removeFilter(iSFilter $filter);

    // :

    /**
     * Get the position of the file pointer
     *
     * @return int
     */
    function getCurrOffset();

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF();

    /**
     * Checks If Stream Is Local One Or Not?
     *
     * @return boolean
     */
    function isLocal();

    /**
     * Is Stream Alive?
     *
     * - resource availability
     *
     * @return boolean
     */
    function isAlive();

    /**
     * Check Whether Stream Resource Is Readable?
     *
     * @return boolean
     */
    function isReadable();

    /**
     * Check Whether Stream Resource Is Writable?
     *
     * @return boolean
     */
    function isWritable();

    /**
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable();
}
