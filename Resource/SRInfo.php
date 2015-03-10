<?php
namespace Poirot\Stream\Resource;

use Poirot\Stream\Interfaces\Filter\iSFilter;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\Resource\iSResMetaReader;

class SRInfo implements iSResource
{
    /**
     * @var resource
     */
    protected $rHandler;

    /**
     * @var SRInfoMeta
     */
    protected $__rMetaInfo;

    /**
     * Construct
     *
     * @param resource $rHandler
     */
    function __construct($rHandler)
    {
        if (!is_resource($rHandler))
            throw new \InvalidArgumentException(sprintf(
                '"%s" given instead of stream resource.',
                is_object($rHandler) ? get_class($rHandler) : gettype($rHandler)
            ));

        $this->rHandler = $rHandler;
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
        if (!is_resource($this->rHandler))
            throw new \Exception(
                'Resource not still available, it might be closed.'
            );

        return $this->rHandler;
    }

    /**
     * Retrieve the name of the local sockets
     *
     * @return string
     */
    function getLocalName()
    {
        return stream_socket_get_name($this->getRHandler(), false);
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
        return stream_socket_get_name($this->getRHandler(), true);
    }

    /**
     * Meta Data About Handler
     *
     * @return iSResMetaReader
     */
    function meta()
    {
        if (!$this->__rMetaInfo)
            $this->__rMetaInfo = new SRInfoMeta($this->getRHandler());

        return $this->__rMetaInfo;
    }

    /**
     * Append Filter
     *
     * [code]
     *  $filter->appendTo($this)
     * [/code]
     *
     * @param iSFilter $filter
     * @param int $rwFlag @see iSFilter::AppendTo
     *
     * @return $this
     */
    function appendFilter(iSFilter $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        // TODO: Implement appendFilter() method.
    }

    /**
     * Attach a filter to a stream
     *
     * @param iSFilter $filter
     * @param int $rwFlag
     *
     * @return $this
     */
    function prependFilter(iSFilter $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        // TODO: Implement prependFilter() method.
    }

    /**
     * Get the position of the file pointer
     *
     * Note: Because PHP's integer type is signed and many platforms
     *       use 32bit integers, some filesystem functions may return
     *       unexpected results for files which are larger than 2GB.
     *
     * @return int
     */
    function getCurrOffset()
    {
        return ftell($this->getRHandler());
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        return feof($this->getRHandler());
    }

    /**
     * Checks If Stream Is Local One Or Not?
     *
     * @return boolean
     */
    function isLocal()
    {
        return stream_is_local($this->getRHandler());
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
        return is_resource($this->rHandler);
    }

    /**
     * Check Whether Stream Resource Is Readable?
     *
     * @return boolean
     */
    function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Writable?
     *
     * @return boolean
     */
    function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }
}
 