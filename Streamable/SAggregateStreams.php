<?php
namespace Poirot\Stream\Streamable;

use Traversable;

use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\Streamable;
use Poirot\Stream\Streamable\AggregateStreams\ResourceAggregate;

/*
$aggrStream = new AggregateStream([
    new TemporaryStream('Hello ...'),
    new TemporaryStream(' Stream Worlds')
]);
echo $aggrStream->read(); // Hello ... Stream Worlds
*/

class SAggregateStreams 
    extends Streamable
    implements iStreamable
    , \IteratorAggregate
{
    /** @var Streamable[] */
    protected $streams = array();

    /** @var ResourceAggregate */
    protected $resource;

    /** @var int Current Offset */
    protected $currOffset;
    /** @var int Current Stream */
    protected $_curr_stream__index = 0;


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

        parent::__construct(null); // no resource
    }

    /**
     * Append Stream
     *
     * // TODO rewind stream if possible
     * // TODO tag stream by name for reading seek
     *
     * @param iStreamable $stream
     *
     * @return $this
     */
    function addStream(iStreamable $stream)
    {
        if (!$stream->resource()->isReadable())
            throw new \InvalidArgumentException(sprintf(
                'Stream "%s" is not readable.'
                , \Poirot\Std\flatten($stream)
            ));


        $this->streams[] = $stream;
        return $this;
    }

    /**
     * Set Stream Handler Resource
     *
     * @param iResourceStream $handle
     *
     * @throws \Exception
     * @return $this
     */
    function setResource(iResourceStream $handle)
    {
        throw new \Exception('Aggregate Stream setResource not implemented.');
    }

    /**
     * Get Stream Handler Resource
     *
     * @return iResourceStream
     */
    function resource()
    {
        if (!$this->resource)
            $this->resource = new ResourceAggregate($this);

        return $this->resource;
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
    function read($inByte = null, $debug = false)
    {
        $inByte = ($inByte === null)
            ?
            (
                ( $this->getBuffer() === null ) ? -1 : $this->getBuffer()
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

            if ($debug) {
                k($this->streams[1]->read());
                kd($this->streams[1]->seek(0, SEEK_SET, true)->read());
            }

            if ($result == null && $currStream->isEOF()) {
                ## loose comparison to match on '', false, and null
                $this->_curr_stream__index++; ## next stream
                continue;
            }
            
            if (function_exists('mb_strlen'))
                $transCount += mb_strlen($result, '8bit');
            else
                $transCount += strlen($result);


            $rData .= $result;
            $inByte = ($inByte == -1) ? $inByte : $inByte - $transCount;
        }


        $this->_resetTransCount($transCount);
        ## move current offset in resource
        $this->currOffset += $this->getTransCount();

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
        $line = null; $i = 1;
        while ( '' !== $rData = $this->read(1) )
        {
            $line .= $rData;
            
            if ($rData === $ending[$i-1]) {
                if ($i == strlen($ending)) {
                    $line = trim($line, $ending);
                    break;
                }
                $i++;
            } else {
                $i = 1;
            }

            if ($inByte !== null) {
                if (strlen($line) >= $inByte)
                    break;
            }
        }

        return $line;
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
        if (!$this->resource()->isSeekable())
            throw new \RuntimeException('This AggregateStream is not seekable.');
        elseif ($whence !== SEEK_SET)
            throw new \RuntimeException('The AggregateStream can only seek with SEEK_SET.');


        $this->currOffset = $this->_curr_stream__index = 0;

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
        while ($this->getCurrOffset() < $offset && !$this->isEOF()) {
            $result = $this->read(min(8096, $offset - $this->getCurrOffset()));
            if ($result == null)
                break;
        }

        return $this;
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
        return $this->currOffset;
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        $streams = $this->streams;
        return empty($streams) || (
            $this->_curr_stream__index + 1 >= count($streams)
            && $streams[$this->_curr_stream__index]->isEOF()
        );
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
