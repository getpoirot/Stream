<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\Interfaces\iStreamable;

/*
// Covert Psr StreamInterface into Streamable:
// ++
$psr    = new StreamPsr('http://google.com/');
$stream = new Streamable(new SResource($psr));
echo $stream->read();

*/

class Streamable 
    implements iStreamable
{
    /** @var iResourceStream */
    protected $resource;

    /** @var int Transaction Count Bytes */
    protected $_transCount;

    protected $_buffer;

    /**
     * Construct
     *
     * @param iResourceStream $resource
     */
    function __construct(iResourceStream $resource = null)
    {
        if ($resource !== null)
            $this->setResource($resource);
    }

    /**
     * Set Stream Handler Resource
     *
     * @param iResourceStream $resource
     *
     * @return $this
     */
    function setResource(iResourceStream $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get Stream Handler Resource
     *
     * @return ResourceStream|iResourceStream|null
     */
    function resource()
    {
        return $this->resource;
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
        $this->_buffer = $buffer;
        return $this;
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
        return $this->_buffer;
    }

    /**
     * Copies Data From One Stream To Another
     *
     * - If maxlength is not specified,
     *   all remaining content in source will be copied
     *
     * @param iStreamable $destStream The destination stream
     * @param null        $maxByte    Maximum bytes to copy
     * @param int         $offset     The offset where to start to copy data, null mean current
     *
     * @return $this
     */
    function pipeTo(iStreamable $destStream, $maxByte = null, $offset = null)
    {
        $this->_assertStreamAlive();

        $maxByte = ($maxByte === null)
            ?
            (
                ($this->getBuffer() === null) ? -1 : $this->getBuffer()
            )
            : $maxByte;


        if ($offset !== null)
            $this->seek($offset);

        ## copy data
        #
        $data  = $this->read($maxByte);
        $destStream->write($data);
        $this->_resetTransCount($destStream->getTransCount());

        return $this;


        /*
        $buffBytes = 8192; $totalBytes = 0;
        while ('' !== $data = $this->read($buffBytes))
        {
            $destStream->write($data);

            $readBytes = $this->getTransCount();
            $totalBytes+=$readBytes;

            if ($maxByte > 0)
                $buffBytes = ($maxByte - $readBytes < 1024)
                    ? $maxByte - $readBytes
                    : 1024;
        }


        $this->_resetTransCount($totalBytes);

        return $this;
        */
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
        $this->_assertReadable();

        $inByte = ($inByte === null)
            ?
            (
                ($this->getBuffer() === null) ? -1 : $this->getBuffer()
            )
            : $inByte;

        $stream = $this->resource()->getRHandler();
        $data   = stream_get_contents($stream, $inByte);
        if (false === $data)
            throw new \RuntimeException('Cannot read stream.');

        if (function_exists('mb_strlen'))
            $transCount = mb_strlen($data, '8bit');
        else
            $transCount = strlen($data);

        $this->_resetTransCount($transCount);

        return $data;
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
        $this->_assertReadable();

        $inByte = ($inByte === null)
            ?
            (
                // buffer must be greater than zero
                (!$this->getBuffer()) ? 1024 : $this->getBuffer()
            )
            : $inByte;

        $stream = $this->resource()->getRHandler();
        if ($ending == "\r" || $ending == "\n" || $ending == "\r\n") {
            // php7 stream_get_line is too slow!!!! so i use default fgets instead in this case
            $data   = fgets($stream, $inByte);
            if (false !== $i = strpos($data, $ending))
                ## found ending in string
                $data = substr($data, 0, $i);
        }
        else 
            // does not return the delimiter itself
            $data   = stream_get_line($stream, $inByte, $ending);

        if (false === $data)
            return null;

        if (function_exists('mb_strlen'))
            $transCount = mb_strlen($data, '8bit');
        else
            $transCount = strlen($data);

        $this->_resetTransCount($transCount);

        return $data;
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
        $this->_assertWritable();

        $stream = $this->resource()->getRHandler();

        $inByte = ($inByte === null)
            ? $this->getBuffer()
            : $inByte;


        $content = (string) $content;
        if (null === $inByte)
            $ret = fwrite($stream, $content);
        else
            $ret = fwrite($stream, $content, $inByte);

        if (false === $ret)
            throw new \RuntimeException('Cannot write on stream.');

        $transCount = $inByte;
        if ($transCount === null) {
            if (function_exists('mb_strlen'))
                $transCount = mb_strlen($content, '8bit');
            else
                $transCount = strlen($content);
        }

        $this->_resetTransCount($transCount);
        return $this;
    }

        /**
         * Note: Writing to a network stream may end before the whole string
         *       is written. Return value of fwrite() may be checked.
         */
        protected function __write_stream($rHandler, $content)
        {
            for ($written = 0; $written < strlen($content); $written += $fwrite) {
                $fwrite = fwrite($rHandler, substr($content, $written));
                if ($fwrite === false)
                    return $written;
            }

            return $written;
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
        $rHandler = $this->resource()->getRHandler();

        if ($flags === null) {
            if ($this->resource()->meta()->getStreamType() == 'udp_socket')
                // STREAM_OOB data not provided on udp sockets
                $flags = STREAM_PEEK;
            else
                $flags = STREAM_SOCK_RDM;
        }

        $ret = @stream_socket_sendto($rHandler, $data, $flags);

        if ($ret == -1) {
            $lerror = error_get_last();
            throw new \RuntimeException(sprintf(
                'Cannot send data on stream, %s.',
                $lerror['message']
            ));
        }


        $this->_resetTransCount($ret);

        return $this;
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
        return stream_socket_recvfrom($this->resource()->getRHandler(), 1024);
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        $size = fstat(
            $this->resource()->getRHandler()
        );

        return $size['size'];
    }

    /**
     * Get Total Count Of Bytes After Each Read/Write
     *
     * @return int
     */
    function getTransCount()
    {
        return $this->_transCount;
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
     *
     * @return $this
     */
    function seek($offset, $whence = SEEK_SET)
    {
        $this->_assertSeekable();

        $stream = $this->resource()->getRHandler();

        if (-1 === fseek($stream, $offset, $whence))
            throw new \RuntimeException('Cannot seek on stream');

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
        return ftell($this->resource()->getRHandler());
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
        $this->_assertSeekable();

        $stream = $this->resource()->getRHandler();

        if (false === rewind($stream))
            throw new \RuntimeException('Cannot rewind stream');

        return $this;
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        if ($this->resource()->meta()->getWrapperType())
            // Wrapper Stream ...
            return $this->resource()->meta()->isReachedEnd();

        return feof($this->resource()->getRHandler());
    }

    // ...

    protected function _assertStreamAlive()
    {
        if (!$this->resource()->isAlive()
            || (
                $this->resource()->meta()
                && $this->resource()->meta()->isTimedOut()
            )
        ) {
            throw new \Exception('Stream is not alive it can be closed or timeout.');
        }
    }

    protected function _assertSeekable()
    {
        $this->_assertStreamAlive();

        if (!$this->resource()->isSeekable())
            throw new \Exception('Cannot seek on a non-seekable stream');
    }

    protected function _assertReadable()
    {
        $this->_assertStreamAlive();

        if (!$this->resource()->isReadable())
            throw new \Exception(sprintf(
                'Cannot read on a non readable stream (current mode is %s)'
                , $this->resource()->meta()->getAccessType()
            ));
    }

    protected function _assertWritable()
    {
        $this->_assertStreamAlive();

        if (!$this->resource()->isWritable())
            throw new \Exception(sprintf(
                'Cannot write on a non-writable stream (current mode is %s)'
                , $this->resource()->meta()->getAccessType()
            ));
    }

    protected function _resetTransCount($count = 0)
    {
        $this->_transCount = $count;
    }
}
 
