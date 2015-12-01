<?php
namespace Poirot\Stream\Psr;

use Poirot\Core\ErrorStack;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\SResource;
use Poirot\Stream\Streamable;
use Poirot\Stream\WrapperClient;

class StreamPsr implements StreamInterface
{
    /** @var Streamable */
    protected $stream;

    /**
     * Construct
     *
     * ! it can be used as decorator for Poirot Stream Into Psr
     *
     * @param string|resource|iSResource|Streamable $stream
     * @param string                                $mode Mode with which to open stream
     *
     * @throws \InvalidArgumentException
     */
    function __construct($stream, $mode = 'r')
    {
        if ($stream instanceof iStreamable) {
            $this->stream = $stream;
            return;
        }

        // ...

        $resource = $stream;
        if (is_resource($stream)) {
            $resource = new SResource($stream);
        } elseif (is_string($stream))
        {
            ErrorStack::handleError(E_WARNING, function ($errno, $errstr) {
                throw new \InvalidArgumentException(
                    'Invalid file provided for stream; must be a valid path with valid permissions'
                );
            });

            $resource = (new WrapperClient($stream, $mode))->getConnect();

            ErrorStack::handleDone();
        }

        if (!$resource instanceof iSResource)
            throw new \InvalidArgumentException(sprintf(
                'Invalid stream provided; must be a string stream identifier or resource.'
                . ' given: "%s"'
                , \Poirot\Core\flatten($resource)
            ));

        $this->stream = new Streamable($resource);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    function __toString()
    {
        $content = '';
        while ($r = $this->stream->read())
            $content .= $r;

        return $r;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    function close()
    {
        if ($this->stream)
            $this->detach();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    function detach()
    {
        $this->stream->getResource()->close();

        return $this->stream = null;
    }

    protected function __assertUsable()
    {
        $return = true;
        if (null === $this->stream || !$this->stream->getResource()->isAlive())
            $return = false;

        return $return;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        if ($this->__assertUsable() === false)
            return null;

        $size = fstat(
            $this->stream->getResource()->getRHandler()
        )['size'];

        return $size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    function tell()
    {
        if (!$this->__assertUsable())
            throw new \RuntimeException('No resource available; cannot tell position');

        return $this->stream->getResource()->getCurrOffset();
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    function eof()
    {
        if (!$this->__assertUsable())
            return true;

        return $this->stream->getResource()->isEOF();
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    function isSeekable()
    {
        if (!$this->__assertUsable())
            return false;

        return $this->stream->getResource()->isSeekable();
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->__assertUsable())
            throw new \RuntimeException('No resource available; cannot seek position');

        $this->stream->seek($offset, $whence);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    function isWritable()
    {
        if (!$this->__assertUsable())
            return false;

        return $this->stream->getResource()->isWritable();
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    function write($string)
    {
        if (!$this->__assertUsable())
            throw new \RuntimeException('No resource available; cannot write');

        $this->stream->write($string);

        return $this->stream->getTransCount();
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    function isReadable()
    {
        if (!$this->__assertUsable())
            return false;

        return $this->stream->getResource()->isReadable();
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    function read($length)
    {
        if (!$this->__assertUsable())
            throw new \RuntimeException('No resource available; cannot read');

        return $this->stream->read($length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    function getContents()
    {
        if (!$this->isReadable())
            return '';

        $content = '';
        while (!$this->eof()) {
            $buf = $this->read(1048576);
            // Using a loose equality here to match on '' and false.
            if ($buf == null)
                break;

            $content .= $buf;
        }

        return $content;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    function getMetadata($key = null)
    {
        if (!$this->__assertUsable())
            return ($key === null) ? [] : null;

        $meta = $this->stream->getResource()->meta();
        $meta = $meta->toArray();

        ## ! compatible for Poirot Messages access to body stream
        $meta['resource'] = $this->stream->getResource()->getRHandler();
        if ($key === null)
            return $meta;

        return (array_key_exists($key, $meta)) ? $meta[$key] : null;
    }
}
