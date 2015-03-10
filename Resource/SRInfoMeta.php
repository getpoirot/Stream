<?php
namespace Poirot\Stream\Resource;

use Poirot\Stream\Interfaces\Resource\iSResMetaReader;

class SRInfoMeta implements iSResMetaReader
{
    /**
     * @var resource
     */
    protected $rHandler;

    /**
     * @var array
     */
    protected $__metaData;

    /**
     * Construct
     *
     * @param resource $resOrigin
     */
    function __construct($resOrigin)
    {
        $this->setRHandler($resOrigin);
    }

    /**
     * Set Original Resource Handler
     *
     * @param resource $resOrigin
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function setRHandler($resOrigin)
    {
        if (!is_resource($resOrigin))
            throw new \InvalidArgumentException(sprintf(
                'This is not valid stream resource. given: "%s"',
                is_object($resOrigin) ? get_class($resOrigin) : gettype($resOrigin)
            ));

        $this->rHandler = $resOrigin;

        return $this;
    }

    /**
     * Live Tracking of Data
     */
    protected function assertMetaData()
    {
        if (!is_resource($this->rHandler))
            throw new \Exception(
                'Resource not still available, it might be closed.'
            );

        $this->__metaData  = stream_get_meta_data($this->rHandler);
    }

    /**
     * The URI/filename Associated With This Stream
     *
     * @return string
     */
    function getUri()
    {
        $this->assertMetaData();

        // TODO: Implement getUri() method.
    }

    /**
     * The Number Of Bytes Currently Contained In The
     * PHP's Own Internal Buffer
     *
     * @return int
     */
    function getUnreadBytes()
    {
        $this->assertMetaData();

        return $this->__metaData['unread_bytes'];
    }

    /**
     * A Label Describing The Underlying Implementation
     * Of The Stream
     *
     * @return string
     */
    function getStreamType()
    {
        $this->assertMetaData();

        return $this->__metaData['stream_type'];
    }

    /**
     * A Label Describing The Protocol Wrapper Implementation
     * Layered Over The Stream
     *
     * @return string
     */
    function getWrapperType()
    {
        $this->assertMetaData();

        // TODO: Implement getWrapperType() method.
    }

    /**
     * Wrapper Specific Data Attached To This Stream
     *
     * @return mixed
     */
    function getWrapperData()
    {
        $this->assertMetaData();

        // TODO: Implement getWrapperData() method.
    }

    /**
     * The Type Mode Of Access Required For This Stream
     *
     * @return string
     */
    function getAccessType()
    {
        $this->assertMetaData();

        return $this->__metaData['mode'];
    }

    /**
     * Is Stream Timed Out While Waiting For
     * Data On The Last Call?
     *
     * @return boolean
     */
    function isTimedOut()
    {
        $this->assertMetaData();

        return (bool) $this->__metaData['timed_out'];
    }

    /**
     * Whether The Current Stream Can Be seeked?
     *
     * @return boolean
     */
    function isSeekable()
    {
        $this->assertMetaData();

        return (bool) $this->__metaData['seekable'];
    }

    /**
     * Is The Stream In None-Blocking IO Mode?
     *
     * @return boolean
     */
    function isNoneBlocking()
    {
        $this->assertMetaData();

        return ! ( (bool) $this->__metaData['blocked'] );
    }

    /**
     * Is The Stream Has Reached End-Of-File?
     *
     * ! this can be TRUE even when unread_bytes is non-zero
     *
     * @return boolean
     */
    function isReachedEnd()
    {
        $this->assertMetaData();

        return (bool) $this->__metaData['eof'];
    }
}
 