<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\Filter\ipSFilter;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\Resource\iSResMetaReader;
use Poirot\Stream\Psr\StreamInterface;
use Poirot\Stream\Resource\SRInfoMeta;
use Poirot\Stream\Wrapper\SPsrWrapper;

class SResource implements iSResource
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
     * resource of attached filters by stream_app/prepend_filter
     * @var array[resource]
     */
    protected $attachedFilters = [];


    /**
     * Construct
     *
     * ! the StreamInterface as argument can be used
     *   it will converted into resource by psr wrapper
     *
     * @param resource|StreamInterface $sResource
     */
    function __construct($sResource)
    {
        if ($sResource instanceof StreamInterface)
            $sResource = SPsrWrapper::convertToResource($sResource);

        if (!is_resource($sResource))
            throw new \InvalidArgumentException(sprintf(
                '(%s) given instead of stream resource.',
                \Poirot\Std\flatten($sResource)
            ));

        $this->rHandler = $sResource;
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
     * @return SRInfoMeta|iSResMetaReader
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
     * @param ipSFilter $filter
     * @param int $rwFlag @see iSFilter::AppendTo
     *
     * @return $this
     */
    function appendFilter(ipSFilter $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        $filterRes = $filter->appendTo($this);

        // store attached filter resource, so we can remove it from stream handler later
        $this->attachedFilters[$filter->getLabel()] = $filterRes;
        return $this;
    }

    /**
     * Attach a filter to a stream
     *
     * @param ipSFilter $filter
     * @param int $rwFlag
     *
     * @return $this
     */
    function prependFilter(ipSFilter $filter, $rwFlag = STREAM_FILTER_ALL)
    {
        $filterRes = $filter->prependTo($this);

        // store attached filter resource, so we can remove it from stream handler later
        $this->attachedFilters[$filter->getLabel()] = $filterRes;
        return $this;
    }

    /**
     * Remove Given Filter From Resource
     *
     * @param ipSFilter $filter
     *
     * @return $this
     */
    function removeFilter(ipSFilter $filter)
    {
        $filterName = $filter->getLabel();
        if (isset($this->attachedFilters[$filterName])) {
            $filterRes = $this->attachedFilters[$filterName];

            stream_filter_remove($filterRes);
            unset($this->attachedFilters[$filterName]); // filter was removed
        }

        return $this;
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
        $allowRead = $this->meta()->getAccessType()
            ->hasAllowRead();

        return $allowRead;
    }

    /**
     * Check Whether Stream Resource Is Writable?
     *
     * @return boolean
     */
    function isWritable()
    {
        $allowWrite = $this->meta()->getAccessType()
            ->hasAllowWrite();

        return $allowWrite;
    }

    /**
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable()
    {
        return $this->meta()->isSeekable();
    }

    /**
     * Close Stream Resource
     *
     * @return null
     */
    function close()
    {
        if ($this->isAlive())
            fclose($this->getRHandler());
    }

    /**
     * destruct,
     * close connection
     *
     */
    function __destruct()
    {
        $this->close();
    }
}
