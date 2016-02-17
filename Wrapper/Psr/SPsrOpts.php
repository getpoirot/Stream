<?php
namespace Poirot\Stream\Wrapper\Psr;

use Poirot\Std\Struct\OpenOptions;
use Poirot\Stream\Psr\StreamInterface;

class SPsrOpts extends OpenOptions
{
    /** @var StreamInterface */
    protected $stream;

    /**
     * Get Stream
     *
     * @return StreamInterface
     */
    function getStream()
    {
        return $this->stream;
    }

    /**
     * Set Stream
     *
     * @param StreamInterface $stream
     *
     * @return $this
     */
    function setStream(StreamInterface $stream)
    {
        $this->stream = $stream;

        return $this;
    }
}
