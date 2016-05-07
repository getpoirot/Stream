<?php
namespace Poirot\Stream\Streamable\AggregateStreams;

use Poirot\Stream\Interfaces\Filter\iFilterStream;
use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\Interfaces\Resource\iMetaReaderOfPhpResource;
use Poirot\Stream\Streamable\SAggregateStreams;

class ResourceAggregate 
    implements iResourceStream
{
    /** @var SAggregateStreams */
    protected $_aggrStream;

    /**
     * Construct
     *
     * @param SAggregateStreams $aggrStream
     */
    function __construct(SAggregateStreams $aggrStream)
    {
        $this->_aggrStream = $aggrStream;
    }

    /**
     * Get Resource Origin Handler
     *
     * - check for resource to be available
     *
     * @throws \Exception On Closed/Not Available Resource
     * @return resource
     */
    function getRHandler()
    {
        throw new \Exception(
            'Resource not available in aggregated streams.'
        );
    }

    /**
     * Append Filter
     *
     * [code]
     *  $filter->appendTo($this)
     * [/code]
     *
     * @param iFilterStream $filter
     * @param int $rwFlag @see iSFilter::AppendTo
     *
     * @return $this
     */
    function appendFilter(iFilterStream $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            $strm->getResource()->appendFilter($filter, $rwFlag);

        return $this;
    }

    /**
     * Attach a filter to a stream
     *
     * @param iFilterStream $filter
     * @param int $rwFlag
     *
     * @return $this
     */
    function prependFilter(iFilterStream $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            $strm->getResource()->prependFilter($filter, $rwFlag);

        return $this;
    }

    /**
     * Remove Given Filter From Resource
     *
     * @param iFilterStream $filter
     *
     * @return $this
     */
    function removeFilter(iFilterStream $filter)
    {
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            $strm->getResource()->removeFilter($filter);

        return $this;
    }

    /**
     * Checks If Stream Is Local One Or Not?
     *
     * @return boolean
     */
    function isLocal()
    {
        $isLocal = true;
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            if (!$isLocal &= $strm->getResource()->isLocal())
                break;

        return $isLocal;
    }

    /**
     * Is Stream Alive?
     *
     * - resource availability
     *
     * @return boolean
     */
    function isAlive()
    {
        $isAlive = true;
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            if (!$isAlive &= $strm->getResource()->isAlive())
                break;

        return $isAlive;
    }

    /**
     * Check Whether Stream Resource Is Readable?
     *
     * @return true
     */
    function isReadable()
    {
        return true;
    }

    /**
     * Check Whether Stream Resource Is Writable?
     *
     * @return false
     */
    function isWritable()
    {
        return false;
    }

    /**
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable()
    {
        $isSeekable = true;
        /** @var iStreamable $strm */
        foreach($this->_aggrStream as $strm)
            if (!$isSeekable &= $strm->getResource()->isSeekable())
                break;

        return $isSeekable;
    }

    /**
     * Close Stream Resource
     *
     * @return null
     */
    function close()
    {
        if ($this->isAlive())
            foreach($this->_aggrStream as $strm)
                /** @var iStreamable $strm */
                $strm->getResource()->close();
    }

    /**
     * Retrieve the name of the local sockets
     *
     * @return string
     */
    function getLocalName()
    {
        return null;
    }

    /**
     * Retrieve the name of the remote sockets
     *
     * ! in tcp connections it will return ip address of
     *   remote server (64.233.185.106:80)
     *
     * @return string
     */
    function getRemoteName()
    {
        return null;
    }

    /**
     * Meta Data About Handler
     *
     * - meta may not available for some streams
     *   so it must return false
     *
     * @return iMetaReaderOfPhpResource|false
     */
    function meta()
    {
        return false;
    }
}
