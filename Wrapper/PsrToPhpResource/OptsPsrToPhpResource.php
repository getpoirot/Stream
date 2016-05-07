<?php
namespace Poirot\Stream\Wrapper\PsrToPhpResource;

use Poirot\Std\Struct\DataOptionsOpen;
use Poirot\Stream\Psr\StreamInterface;

class OptsPsrToPhpResource 
    extends DataOptionsOpen
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
