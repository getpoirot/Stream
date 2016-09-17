<?php
namespace Poirot\Stream\Psr;

use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\ResourceStream;
use Poirot\Stream\Streamable;
use Poirot\Stream\Wrapper\WrapperPsrToPhpResource;
use Psr\Http\Message\StreamInterface;


class StreamBridgeFromPsr
    extends Streamable\SDecorateStreamable
    implements iStreamable
{
    /** @var iStreamable */
    protected $streamInterface;


    /**
     * StreamBridgeFromPsr constructor.
     * @param StreamInterface $streamInterface
     */
    function __construct(StreamInterface $streamInterface)
    {
        $this->streamInterface = $streamInterface;

        $resource   = WrapperPsrToPhpResource::convertToResource($streamInterface);
        $resource   = new ResourceStream($resource);
        $streamable = new Streamable($resource);

        parent::__construct($streamable);
    }
}
