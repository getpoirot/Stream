<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Filter\iFilterStream;
use Poirot\Stream\Interfaces\Resource\iMetaReaderOfPhpResource;

interface iResourceStream
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
     * - meta may not available for some streams
     *   so it must return false
     *
     * @return iMetaReaderOfPhpResource|false
     */
    function meta();

    /**
     * Append Filter
     *
     * [code]
     *  $filter->appendTo($this)
     * [/code]
     *
     * @param iFilterStream $filter
     * @param int      $rwFlag  @see iSFilter::AppendTo
     *
     * @return $this
     */
    function appendFilter(iFilterStream $filter, $rwFlag = STREAM_FILTER_ALL);

    /**
     * Attach a filter to a stream
     *
     * @param iFilterStream $filter
     * @param int      $rwFlag
     *
     * @return $this
     */
    function prependFilter(iFilterStream $filter, $rwFlag = STREAM_FILTER_ALL);

    /**
     * Remove Given Filter From Resource
     *
     * @param iFilterStream $filter
     *
     * @return $this
     */
    function removeFilter(iFilterStream $filter);

    // :

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

    /**
     * Close Stream Resource
     *
     * @return null
     */
    function close();
}
