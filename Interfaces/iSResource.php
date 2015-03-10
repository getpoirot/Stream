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

    // :

    /**
     * @link http://php.net/manual/en/function.ftell.php
     *
     * Get the position of the file pointer
     *
     * @return int
     */
    function getCurrOffset();

    /**
     * @link http://php.net/manual/en/function.feof.php
     *
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF();

    /**
     * @link http://php.net/manual/en/function.stream-is-local.php
     *
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
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Readable?
     *
     * @return boolean
     */
    function isReadable();

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Writable?
     *
     * @return boolean
     */
    function isWritable();

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable();
}
