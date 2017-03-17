<?php
namespace Poirot\Stream\Resource;

use Poirot\Stream\Interfaces\Resource\iAccessModeToResourceStream;
use Poirot\Stream\Interfaces\Resource\iMetaReaderOfPhpResource;

class MetaReaderOfPhpResource 
    implements iMetaReaderOfPhpResource
{
    /** @var resource */
    protected $rHandler;

    /** @var array */
    protected $_metaData;
    
    /** @var AccessMode */
    protected $_c__accMode;


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

        $this->_metaData  = stream_get_meta_data($this->rHandler);
    }

    /**
     * Get Meta Key Value
     *
     * @param string $key
     * @param null   $default
     *
     * @return null|mixed
     */
    function getMetaKey($key, $default = null)
    {
        $this->assertMetaData();

        if (isset($this->_metaData[$key]))
            return $this->_metaData[$key];

        return $default;
    }

    /**
     * Get Whole Data as Array
     *
     * @return array
     */
    function toArray()
    {
        $this->assertMetaData();
        return $this->_metaData;
    }

    /**
     * The URI/filename Associated With This Stream
     *
     * @return string
     */
    function getUri()
    {
        $this->assertMetaData();
        return $this->getMetaKey('uri');
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
        return $this->getMetaKey('unread_bytes');
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
        return $this->getMetaKey('stream_type');
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
        return $this->getMetaKey('wrapper_type');
    }

    /**
     * Wrapper Specific Data Attached To This Stream
     *
     * @return mixed
     */
    function getWrapperData()
    {
        $this->assertMetaData();
        return $this->getMetaKey('wrapper_data');
    }

    /**
     * The Type Mode Of Access Required For This Stream
     *
     * @return iAccessModeToResourceStream
     */
    function getAccessType()
    {
        if (!$this->_c__accMode instanceof AccessMode)
            $this->_c__accMode = new AccessMode;

        $this->assertMetaData();
        $mode = $this->getMetaKey('mode');
        if ($mode !== null)
            $this->_c__accMode->fromString($mode);

        return $this->_c__accMode;
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
        return (bool) $this->getMetaKey('timed_out');
    }

    /**
     * Whether The Current Stream Can Be seeked?
     *
     * note: StreamWrapper implementations have no control over
     *       the "seekable" property of stream_get_meta_data()
     *       and always advertise themselves as seekable.
     *
     * @return bool
     */
    function isSeekable()
    {
        $this->assertMetaData();
        
        if ($this->getMetaKey('wrapper_type')) {
            // This is a wrapper resource; challenge seekable ...
            if (false === $pos = ftell($this->rHandler))
                throw new \RuntimeException('Unable to determine stream position');

            try {
                if (-1 === @fseek($this->rHandler, $pos, SEEK_SET))
                    return false;
            } catch (\Exception $e) {
                // Maybe Fallback into wrapper handler for seek
                return false;
            }
        }
        
        return (bool) $this->getMetaKey('seekable');
    }

    /**
     * Is The Stream In None-Blocking IO Mode?
     *
     * @return boolean
     */
    function isNoneBlocking()
    {
        $this->assertMetaData();
        return ! ( (bool) $this->getMetaKey('blocked') );
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
        return (bool) $this->getMetaKey('eof');
    }
}
 