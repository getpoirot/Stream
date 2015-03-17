<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamable;

class Streamable implements iStreamable
{
    /**
     * @var iSResource
     */
    protected $resource;

    /**
     * @var int Transaction Count Bytes
     */
    protected $__transCount;

    /**
     * Construct
     *
     * @param iSResource $resource
     */
    function __construct(iSResource $resource)
    {
       $this->setResource($resource);
    }

    /**
     * Set Stream Handler Resource
     *
     * @param iSResource $resource
     *
     * @return $this
     */
    function setResource(iSResource $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get Stream Handler Resource
     *
     * @return iSResource
     */
    function getResource()
    {
        return $this->resource;
    }

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
        $this->__assertStreamAlive();

        $maxByte = ($maxByte == null) ? -1 : $maxByte;

        $count = stream_copy_to_stream(
            $this->getResource()->getRHandler()
            , $destStream->getResource()->getRHandler()
            , $maxByte
            , $offset
        );

        $this->__resetTransCount($count);

        return $this;
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
        $this->__assertReadable();

        $inByte = ($inByte == null) ? -1 : $inByte;

        $stream = $this->getResource()->getRHandler();
        $data   = stream_get_contents($stream, $inByte);
        if (false === $data)
            throw new \RuntimeException('Cannot read stream.');

        $transCount = mb_strlen($data, '8bit');
        $this->__resetTransCount($transCount);

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
     * @return string
     */
    function readLine($ending = "\n", $inByte = null)
    {
        $this->__assertReadable();

        $inByte = ($inByte == null) ? 1024 : $inByte;

        $stream = $this->getResource()->getRHandler();
        $data   = stream_get_line($stream, $inByte, $ending);
        if (false === $data)
            throw new \RuntimeException('Cannot read stream.');

        $transCount = mb_strlen($data, '8bit');
        $this->__resetTransCount($transCount);

        return $data;
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
        $this->__assertWritable();

        $stream = $this->getResource()->getRHandler();

        if (null === $inByte)
            $ret = fwrite($stream, $content);
        else
            $ret = fwrite($stream, $content, $inByte);

        if (false === $ret)
            throw new \RuntimeException('Cannot write on stream.');

        $transCount = ($inByte !== null) ? $inByte : mb_strlen($content, '8bit');
        $this->__resetTransCount($transCount);

        return $ret;
    }

    /**
     * Sends the specified data through the socket,
     * whether it is connected or not
     *
     * @param string $data The data to be sent
     * @param int $flags The value of flags can be any combination of the following:
     *                      STREAM_OOB    Process OOB (out-of-band) data.
     *
     * @return $this
     */
    function sendData($data, $flags = 0)
    {
        $stream = $this->getResource()->getRHandler();

        /**
         * Provides a RDM (Reliably-delivered messages) socket.
         * STREAM_SOCK_RDM
         * STREAM_PEEK
         * STREAM_OOB
         *
         * @link http://php.net/manual/en/stream.constants.php
         */
        $ret = stream_socket_sendto($stream, "Out of Band data.", STREAM_OOB);
        if ($ret == -1)
            throw new \RuntimeException('Cannot send on stream.');

        $this->__resetTransCount($ret);

        return $this;
    }

    /**
     * Get Total Count Of Bytes After Each Read/Write
     *
     * @return int
     */
    function getTransCount()
    {
        return $this->__transCount;
    }

        protected function __resetTransCount($count = 0)
        {
            $this->__transCount = $count;
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
        $this->__assertSeekable();

        $stream = $this->getResource()->getRHandler();

        if (false === fseek($stream, $offset, $whence))
            throw new \RuntimeException('Cannot seek on stream');

        return $this;
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
        $this->__assertSeekable();

        $stream = $this->getResource()->getRHandler();

        if (false === rewind($stream))
            throw new \RuntimeException('Cannot rewind stream');

        return $this;
    }

    protected function __assertStreamAlive()
    {
        if (!$this->getResource()->isAlive())
            throw new \Exception('Cannot seek on a closed stream');
    }

    protected function __assertSeekable()
    {
        $this->__assertStreamAlive();

        if (!$this->getResource()->isSeekable())
            throw new \Exception('Cannot seek on a non-seekable stream');
    }

    protected function __assertReadable()
    {
        $this->__assertStreamAlive();

        if (!$this->getResource()->isReadable())
            throw new \Exception(sprintf(
                'Cannot read on a non readable stream (current mode is %s)'
                , $this->getResource()->meta()->getAccessType()
            ));
    }

    protected function __assertWritable()
    {
        $this->__assertStreamAlive();

        if (!$this->getResource()->isWritable())
            throw new \Exception(sprintf(
                'Cannot write on a non-writable stream (current mode is %s)'
                , $this->getResource()->meta()->getAccessType()
            ));
    }
}
 