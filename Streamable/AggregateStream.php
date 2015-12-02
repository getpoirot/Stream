<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\Streamable;
use Traversable;

/*
$aggrStream = new AggregateStream([
    new TemporaryStream('Hello ...'),
    new TemporaryStream(' Stream Worlds')
]);
echo $aggrStream->read(); // Hello ... Stream Worlds
*/

class AggregateStream extends Streamable
    implements iStreamable
    , \IteratorAggregate
{
    /** @var Streamable[] */
    protected $streams = [];

    /** @var AggregateResource */
    protected $resource;
    /** @var int Current Stream */
    public $_curr_stream__index = 0;


    /**
     * Construct
     *
     * @param array $streams
     */
    function __construct(array $streams = null)
    {
        if ($streams !== null)
            foreach($streams as $strm)
                $this->addStream($strm);
    }

    /**
     * Append Stream
     *
     * @param iStreamable $stream
     *
     * @return $this
     */
    function addStream(iStreamable $stream)
    {
        if (!$stream->getResource()->isReadable())
            throw new \InvalidArgumentException(sprintf(
                'Stream "%s" is not readable.'
                , \Poirot\Core\flatten($stream)
            ));


        $this->streams[] = $stream;
        return $this;
    }

    /**
     * Set Stream Handler Resource
     *
     * @param iSResource $handle
     *
     * @throws \Exception
     * @return $this
     */
    function setResource(iSResource $handle)
    {
        throw new \Exception('Resource is banned by default.');
    }

    /**
     * Get Stream Handler Resource
     *
     * @return iSResource
     */
    function getResource()
    {
        if (!$this->resource)
            $this->resource = new AggregateResource($this);

        return $this->resource;
    }

    /**
     * Copies Data From One Stream To Another
     *
     * - If maxlength is not specified,
     *   all remaining content in source will be copied
     *
     * - reset and count into transCount
     *
     * @param iStreamable $destStream The destination stream
     * @param null $maxByte Maximum bytes to copy
     * @param int $offset The offset where to start to copy data
     *
     * @return $this
     */
    function pipeTo(iStreamable $destStream, $maxByte = null, $offset = 0)
    {
        $this->__assertStreamAlive();

        $maxByte = ($maxByte === null)
            ?
            (
                ($this->getBuffer() === null) ? -1 : $this->getBuffer()
            )
            : $maxByte;

        ## get current offset
        $currOffset = $this->getResource()->getCurrOffset();
        $this->seek($offset);

        ## copy data
        $data  = $this->read($maxByte);
        $destStream->write($data);
        $this->__resetTransCount($destStream->getTransCount());

        ## get back to current offset
        $this->seek($currOffset);
        return $this;
    }

    /**
     * Read Data From Stream
     *
     * - if $inByte argument not set, read entire stream
     *
     * @param int $inByte Read Data in byte
     *
     * @throws \Exception Error On Read Data
     * @return string
     */
    function read($inByte = null)
    {
        $inByte = ($inByte === null)
            ?
            (
                ($this->getBuffer() === null) ? -1 : $this->getBuffer()
            )
            : $inByte;


        $rData = '';

        $total = count($this->streams); $transCount = 0;
        while ($inByte == -1 || $inByte > 0)
        {
            if ($this->_curr_stream__index +1 > $total)
                ## no more stream to read
                break;

            $currStream = $this->streams[$this->_curr_stream__index];
            $result     = $currStream->read($inByte);
            if ($result == null && $currStream->getResource()->isEOF()) {
                ## loose comparison to match on '', false, and null
                $this->_curr_stream__index++; ## next stream
                continue;
            }

            if (function_exists('mb_strlen'))
                $transCount += mb_strlen($result, '8bit');
            else
                // TODO implement data length without mb_strlen
                $transCount += strlen($result);


            $rData .= $result;
            $inByte = ($inByte == -1) ? $inByte : $inByte - $transCount;
        }


        $this->__resetTransCount($transCount);
        ## move current offset in resource
        $this->getResource()->currOffset += $this->getTransCount();

        return $rData;
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
     * @param int $inByte
     *
     * @return string
     */
    function readLine($ending = "\n", $inByte = null)
    {
        $currOffset = $this->getResource()->getCurrOffset();

        $rData = $this->read($inByte);
        if (($i = strpos($rData, $ending)) !== false) {
            ## found ending in string
            $rData = substr($rData, 0, $i);
            $this->seek($currOffset+$i/*length of data*/+strlen($ending)/* skip ending */);
        }

        return $rData;
    }

    /**
     * Writes the contents of string to the file stream
     *
     * @param string $content The string that is to be written
     * @param int $inByte Writing will stop after length bytes
     *                          have been written or the end of string
     *                          is reached
     *
     * @return $this
     */
    function write($content, $inByte = null)
    {
        throw new \RuntimeException(__FUNCTION__. ' is not implemented in AggregateStream.');
    }

    /**
     * Sends the specified data through the socket,
     * whether it is connected or not
     *
     * @param string $data The data to be sent
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
        throw new \RuntimeException(__FUNCTION__. ' is not implemented in AggregateStream.');
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
        throw new \RuntimeException(__FUNCTION__. ' is not implemented in AggregateStream.');
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        $size = 0;
        foreach ($this->streams as $stream) {
            if (($s = $stream->getSize()) === null)
                return null;

            $size += $s;
        }

        return $size;
    }

    /**
     * @link http://php.net/manual/en/function.fseek.php
     *
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
     *
     * @return $this
     */
    function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->getResource()->isSeekable())
            throw new \RuntimeException('This AggregateStream is not seekable.');
        elseif ($whence !== SEEK_SET)
            throw new \RuntimeException('The AggregateStream can only seek with SEEK_SET.');


        $this->getResource()->currOffset = $this->_curr_stream__index = 0;

        // Rewind each stream
        foreach ($this->streams as $i => $stream)
            try {
                $stream->rewind();
            } catch (\Exception $e) {
                throw new \RuntimeException(
                    'Unable to seek stream '.$i.' of the AggregateStream'
                    , 0, $e
                );
            }

        // Seek to the actual position by reading from each stream
        while ($this->getResource()->getCurrOffset() < $offset && !$this->getResource()->isEOF()) {
            $result = $this->read(min(8096, $offset - $this->getResource()->getCurrOffset()));
            if ($result == null)
                break;
        }
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
        $this->seek(0);
    }


    // ...

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayObject($this->streams);
    }
}
 