<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\iResourceStream;
use Poirot\Stream\ResourceStream;
use Poirot\Stream\Streamable;


class SUpstream
    extends Streamable
{
    /** @var Streamable */
    protected $upstream;
    protected $skipReadBytes;


    /**
     * Construct
     *
     * @param iResourceStream $stream This is the upstream
     * @param iResourceStream $target Stream caching to
     *
     * @throws \Exception
     */
    function __construct(iResourceStream $stream, iResourceStream $target = null)
    {
        parent::__construct(
            $target ?: new ResourceStream(fopen('php://temp', 'r+'))
        );


        $this->upstream = new Streamable($stream);
    }


    /**
     * @inheritdoc
     */
    function read($inByte = null)
    {
        $data = parent::read($inByte);
        $len  = parent::getTransCount();
        $remaining = $inByte - $len;

        // More data was requested so read from the remote stream
        $lenUpstream = 0;
        if ($remaining) {
            // If data was written to the buffer in a position that would have
            // been filled from the remote stream, then we must skip bytes on
            // the remote stream to emulate overwriting bytes from that
            // position. This mimics the behavior of other PHP stream wrappers.
            $remoteData = $this->upstream->read(
                $remaining + $this->skipReadBytes
            );

            if ($this->skipReadBytes) {
                $lenUpstream = $this->upstream->getTransCount();
                $remoteData  = substr($remoteData, $this->skipReadBytes);
                $this->skipReadBytes = max(0, $this->skipReadBytes - $lenUpstream);
            }

            $data .= $remoteData;
            parent::write($remoteData);
        }


        $this->_resetTransCount($len + $lenUpstream);
        return $data;
    }

    /**
     * @inheritdoc
     */
    function readLine($ending = "\n", $inByte = null)
    {
        $data = parent::readLine($ending, $inByte);
        $len  = parent::getTransCount();
        $remaining = $inByte - $len;

        // More data was requested so read from the remote stream
        $lenUpstream = 0;
        if ($remaining) {
            // If data was written to the buffer in a position that would have
            // been filled from the remote stream, then we must skip bytes on
            // the remote stream to emulate overwriting bytes from that
            // position. This mimics the behavior of other PHP stream wrappers.
            $remoteData = $this->upstream->readLine(
                $ending,
                $remaining + $this->skipReadBytes
            );

            if ($this->skipReadBytes) {
                $lenUpstream = $this->upstream->getTransCount();
                $remoteData  = substr($remoteData, $this->skipReadBytes);
                $this->skipReadBytes = max(0, $this->skipReadBytes - $lenUpstream);
            }

            $data .= $remoteData;
            parent::write($remoteData);
        }


        $this->_resetTransCount($len + $lenUpstream);
        return $data;
    }

    /**
     * @inheritdoc
     */
    function write($content, $inByte = null)
    {
        // When appending to the end of the currently read stream, you'll want
        // to skip bytes from being read from the remote stream to emulate
        // other stream wrappers. Basically replacing bytes of data of a fixed
        // length.
        $overflow = (strlen($content) + $this->getCurrOffset()) - $this->upstream->getCurrOffset();
        if ($overflow > 0)
            $this->skipReadBytes += $overflow;


        parent::write($content);
        return $this;
    }

    /**
     * @inheritdoc
     */
    function seek($offset, $whence = SEEK_SET)
    {
        if ($whence == SEEK_SET)
            $byte = $offset;
        elseif ($whence == SEEK_CUR)
            $byte = $offset + $this->getCurrOffset();
        else
            return false;


        if ($byte > parent::getSize())
            throw new \RuntimeException(sprintf(
                'Cannot seek ahead %d byte from buffered stream that contains %d bytes.'
                , $byte
                , parent::getSize()
            ));

        parent::seek($offset, $whence);
        return $this;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    function getSize()
    {
        return max(parent::getSize(), $this->upstream->getSize());
    }

    /**
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF()
    {
        return parent::isEOF() && $this->upstream->isEOF();
    }
}
