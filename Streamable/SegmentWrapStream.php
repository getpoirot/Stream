<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\SResource;
use Poirot\Stream\Streamable;

/**
 * Wrapper Around Stream To Use Subset Of Stream
 */
class SegmentWrapStream extends Streamable
{
    /** @var iStreamable Wrapped Stream */
    protected $_w__stream;

    protected $segmentOffset = 0;
    protected $segmentLimit  = -1;


    /**
     * Construct
     *
     * @param iStreamable $streamable
     * @param int         $limit      Bytes limit can be read from stream
     * @param int         $offset     Start offset of stream that consumed as zero seek
     */
    function __construct(iStreamable $streamable, $limit = -1, $offset = 0)
    {
        $this->_w__stream = $streamable;

        $this->setSegmentLimit($limit);
        $this->setSegmentOffset($offset);

        parent::__construct($streamable->getResource());

        ## ensure wrapped stream is on correct offset
        $this->seek(0);
    }

    // Override:

    /**
     * Copies Data From One Stream To Another
     *
     * - If maxlength is not specified,
     *   all remaining content in source will be copied
     *
     * @param iStreamable $destStream The destination stream
     * @param null        $maxByte    Maximum bytes to copy
     * @param int         $offset     The offset where to start to copy data
     *
     * @return $this
     */
    function pipeTo(iStreamable $destStream, $maxByte = null, $offset = 0)
    {
        ## get real offset of wrapped stream
        $this->seek($offset);
        $offset = $this->_w__stream->getCurrOffset();

        return $this->_w__stream->pipeTo($destStream, $maxByte, $offset);
    }

    /**
     * Read Data From Stream
     *
     * - if $inByte argument not set, read entire stream
     *
     * @param int  $inByte Read Data in byte
     *
     * @throws \Exception Error On Read Data
     * @return string
     */
    function read($inByte = null)
    {
        if ($this->getSegmentLimit() == -1)
            return $this->_w__stream->read($inByte);

        $inByte = ($inByte === null)
            ?
            (
                ($this->getBuffer() === null) ? $this->getSize() : $this->getBuffer()
            )
            : $inByte;

        $remain = $this->getSegmentLimit() - $this->getCurrOffset();
        if ($remain > 0)
            return $this->_w__stream->read(min($remain, $inByte));

        return '';
    }

    /**
     * Gets line from stream resource up to a given delimiter
     *
     * Reading ends when length bytes have been read,
     * when the string specified by ending is found
     * (which is not included in the return value),
     * or on EOF (whichever comes first)
     *
     * ! does not return the ending delimiter itself
     *
     * @param string $ending
     * @param int    $inByte
     *
     * @return string|null
     */
    function readLine($ending = "\n", $inByte = null)
    {
        if ($this->getSegmentLimit() == -1)
            return $this->_w__stream->readLine($ending, $inByte);

        $inByte = ($inByte === null)
            ?
            (
            ($this->getBuffer() === null) ? $this->getSize() : $this->getBuffer()
            )
            : $inByte;

        $remain = $this->getSegmentLimit() - $this->getCurrOffset();
        if ($remain > 0)
            return $this->_w__stream->readLine($ending, min($remain, $inByte));

        return null;
    }

    /**
     * Move the file pointer to a new position
     *
     * - The new position, measured in bytes from the beginning of the file,
     *   is obtained by adding $offset to the position specified by $whence.
     *
     * ! php doesn't support seek/rewind on non-local streams
     *   we can using temp/cache piped stream.
     *
     * ! If you have opened the file in append ("a" or "a+") mode,
     *   any data you write to the file will always be appended,
     *   regardless of the file position.
     *
     * @param int $offset
     * @param int $whence Accepted values are:
     *              - SEEK_SET - Set position equal to $offset bytes.
     *              - SEEK_CUR - Set position to current location plus $offset.
     *              - SEEK_END - Set position to end-of-file plus $offset.
     * @return $this
     * @throws \Exception
     */
    function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->_w__stream->getResource()->isSeekable()) {
            $cur = $this->_w__stream->getCurrOffset();
            if ($cur > $this->getSegmentOffset())
                throw new \RuntimeException('Could not seek to stream offset.');

            ## when stream is not seekable read til offset
            $this->_w__stream->read($offset);
            return $this;
        }

        $offset += $this->getSegmentOffset();

        $endOffset = $this->getSegmentOffset() + $this->getSegmentLimit();
        if ($this->getSegmentLimit() !== -1
            && $offset > $endOffset
        )
            $offset = $endOffset;

        return $this->_w__stream->seek($offset, $whence);
    }

    /**
     * Move the file pointer to the beginning of the stream
     *
     * ! php doesn't support seek/rewind on non-local streams
     *   we can using temp/cache piped stream.
     *
     * ! If you have opened the file in append ("a" or "a+") mode,
     *   any data you write to the file will always be appended,
     *   regardless of the file position.
     *
     * @return $this
     */
    function rewind()
    {
        return $this->seek(0);
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
        return $this->_w__stream->getCurrOffset() - $this->getSegmentOffset();
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        if ($this->_w__stream->isEOF())
            ## wrap stream is on the end
            return true;

        if ($this->getSegmentLimit() == -1)
            return false;

        return ($this->_w__stream->getCurrOffset() >= $this->getSegmentOffset() + $this->getSegmentLimit());
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        $size = $this->_w__stream->getSize();
        if ($size !== null) {
            $segmentSize = $size - $this->getSegmentOffset();
            $size = ($this->getSegmentLimit() == -1)
                ? $segmentSize
                : min($segmentSize - $this->getSegmentLimit(), $this->getSegmentLimit());
        }

        return $size;
    }

    // options:

    /**
     * Bytes limit can be read from stream
     * @param int $limit
     * @return $this
     */
    public function setSegmentLimit($limit)
    {
        $this->segmentLimit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getSegmentLimit()
    {
        return $this->segmentLimit;
    }

    /**
     * Start offset of stream that consumed as zero seek
     * @param int $offset
     * @return $this
     */
    public function setSegmentOffset($offset)
    {
        $this->segmentOffset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getSegmentOffset()
    {
        return $this->segmentOffset;
    }

    // Wrap:

    /**
     * Set Stream Handler Resource
     *
     * @param iSResource $resource
     *
     * @return $this
     */
    function setResource(iSResource $resource)
    {
        return $this->_w__stream->setResource($resource);
    }

    /**
     * Get Stream Handler Resource
     *
     * @return SResource|iSResource
     */
    function getResource()
    {
        return $this->_w__stream->getResource();
    }

    /**
     * Set R/W Buffer Size
     *
     * @param int|null $buffer
     *
     * @return $this
     */
    function setBuffer($buffer)
    {
        return $this->_w__stream->setBuffer($buffer);
    }

    /**
     * Get Current R/W Buffer Size
     *
     * - usually null mean all stream content
     * - used as default $inByte argument value on
     *   read/write methods
     *
     * @return int|null
     */
    function getBuffer()
    {
        return $this->_w__stream->getBuffer();
    }

    /**
     * Sends the specified data through the socket,
     * whether it is connected or not
     *
     * @param string   $data  The data to be sent
     * @param int|null $flags Provides a RDM (Reliably-delivered messages) socket
     *                        The value of flags can be any combination of the following:
     *                        - STREAM_SOCK_RDM
     *                        - STREAM_PEEK
     *                        - STREAM_OOB       process OOB (out-of-band) data
     *                        - null             auto choose the value
     *
     * @return $this
     */
    function sendData($data, $flags = null)
    {
        return $this->_w__stream->sendData($data, $flags);
    }

    /**
     * Receives data from a socket, connected or not
     *
     * @param int $maxByte
     * @param int $flags
     *
     * @return string
     */
    function receiveFrom($maxByte, $flags = STREAM_OOB)
    {
        return $this->_w__stream->receiveFrom($maxByte, $flags);
    }

    /**
     * Writes the contents of string to the file stream
     *
     * @param string $content The string that is to be written
     * @param int    $inByte  Writing will stop after length bytes
     *                        have been written or the end of string
     *                        is reached
     *
     * @return $this
     */
    function write($content, $inByte = null)
    {
        return $this->_w__stream->write($content, $inByte);
    }

    /**
     * Get Total Count Of Bytes After Each Read/Write
     *
     * @return int
     */
    function getTransCount()
    {
        return $this->_w__stream->getTransCount();
    }
}
