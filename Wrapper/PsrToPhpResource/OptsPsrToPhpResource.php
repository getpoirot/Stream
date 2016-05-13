<?php
namespace Poirot\Stream\Wrapper\PsrToPhpResource;

use Psr\Http\Message\StreamInterface;

use Poirot\Std\Struct\DataOptionsOpen;

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
