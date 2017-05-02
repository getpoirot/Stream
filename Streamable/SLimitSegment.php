<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\iStreamable;


/**
 * Wrapper Around Stream To Use Subset Of Stream
 */
class SLimitSegment
    extends SDecorateStreamable
{
    protected $segmentOffset = 0;
    protected $segmentLimit  = -1;


    /**
     * Construct
     *
     * @param iStreamable $streamable
     * @param int         $limit      Bytes limit can be read from stream
     * @param int|null    $offset     Start offset of stream that consumed as zero seek; null mean current offset
     */
    function __construct(iStreamable $streamable, $limit = -1, $offset = null)
    {
        $this->setSegmentLimit($limit);

        if ($offset === null)
            $offset = $streamable->getCurrOffset();

        $this->setSegmentOffset($offset);

        parent::__construct($streamable);
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
        $offset = $this->_t__wrap_stream->getCurrOffset();

        return $this->_t__wrap_stream->pipeTo($destStream, $maxByte, $offset);
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
            return $this->_t__wrap_stream->read($inByte);


        $inByte = ($inByte === null)
            ? (
                ($this->getBuffer() === null) ? $this->getSize() : $this->getBuffer()

            ) : $inByte;

        $remain = $this->getSegmentLimit() - $this->getCurrOffset();
        if ($remain > 0)
            return $this->_t__wrap_stream->read(min($remain, $inByte));

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
            return $this->_t__wrap_stream->readLine($ending, $inByte);


        $inByte = ($inByte === null)
            ? (
                ($this->getBuffer() === null) ? $this->getSize() : $this->getBuffer()

            ) : $inByte;

        $remain = $this->getSegmentLimit() - $this->getCurrOffset();
        if ($remain > 0)
            return $this->_t__wrap_stream->readLine($ending, min($remain, $inByte));

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
        // offset at 0 has equal to segment when given
        $offset += $this->getSegmentOffset();

        $endOffset = $this->getSegmentOffset() + $this->getSegmentLimit();
        if ($this->getSegmentLimit() !== -1 && $offset > $endOffset)
            // offset not exceed the limit when given ....
            $offset = $endOffset;


        if (! $this->_t__wrap_stream->resource()->isSeekable() )
        {
            // when stream is not seekable read til offset

            $cur = $this->_t__wrap_stream->getCurrOffset();
            if ($cur > $this->getSegmentOffset())
                throw new \RuntimeException('Could not seek to stream offset.');

            if ( 0 < $byte = $offset - $cur )
                $this->_t__wrap_stream->read($byte);

            return $this;
        }

        return $this->_t__wrap_stream->seek($offset, $whence);
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
        return $this->_t__wrap_stream->getCurrOffset() - $this->getSegmentOffset();
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        if ($this->_t__wrap_stream->isEOF())
            ## wrap stream is on the end
            return true;

        if ($this->getSegmentLimit() == -1)
            return false;

        return ($this->_t__wrap_stream->getCurrOffset() >= $this->getSegmentOffset() + $this->getSegmentLimit());
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        $size = $this->_t__wrap_stream->getSize();
        if ($size !== null) {
            $segmentSize = $size - $this->getSegmentOffset();
            if ($this->getSegmentLimit() == -1)
                $size = $segmentSize;
            elseif ($this->getSegmentLimit() >= $segmentSize)
                $size = $segmentSize;
            else
                $size = min( $segmentSize - $this->getSegmentLimit(), $this->getSegmentLimit() );
        }

        return $size;
    }


    // options:

    /**
     * Bytes limit can be read from stream
     * @param int $limit
     * @return $this
     */
    function setSegmentLimit($limit)
    {
        $this->segmentLimit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    function getSegmentLimit()
    {
        return $this->segmentLimit;
    }

    /**
     * Start offset of stream that consumed as zero seek
     * @param int $offset
     * @return $this
     */
    function setSegmentOffset($offset)
    {
        $this->segmentOffset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    function getSegmentOffset()
    {
        return $this->segmentOffset;
    }
}
