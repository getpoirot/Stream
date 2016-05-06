<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\Filter\iFilterStream;
use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\Resource\iMetaReaderOfPhpResource;
use Poirot\Stream\Psr\StreamInterface;
use Poirot\Stream\Resource\MetaReaderOfPhpResource;
use Poirot\Stream\Wrapper\SPsrWrapper;

class ResourceStream 
    implements iResourceStream
{
    /** @var resource */
    protected $rHandler;

    /** @var MetaReaderOfPhpResource */
    protected $_rMetaInfo;

    /**
     * resource of attached filters by stream_app/prepend_filter
     * @var resource[]
     */
    protected $attachedFilters = array();


    /**
     * Construct
     *
     * ! the StreamInterface as argument can be used
     *   it will converted into resource by PSR wrapper
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
     * @return MetaReaderOfPhpResource|iMetaReaderOfPhpResource
     */
    function meta()
    {
        if (!$this->_rMetaInfo)
            $this->_rMetaInfo = new MetaReaderOfPhpResource($this->getRHandler());

        return $this->_rMetaInfo;
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
        $filterRes = $filter->appendTo($this);

        // store attached filter resource, so we can remove it from stream handler later
        $this->attachedFilters[$filter->getLabel()] = $filterRes;
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
        $filterRes = $filter->prependTo($this);

        // store attached filter resource, so we can remove it from stream handler later
        $this->attachedFilters[$filter->getLabel()] = $filterRes;
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
        // TODO if connection is not persist
        $this->close();
    }
}
