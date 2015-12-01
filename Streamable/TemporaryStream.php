<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\Resource\iSRAccessMode;
use Poirot\Stream\Streamable;
use Poirot\Stream\WrapperClient;

class TemporaryStream extends Streamable
{
    /**
     * Construct
     *
     * @param null|string          $resource
     * @param iSRAccessMode|string $openMode
     */
    function __construct($resource = null, $openMode = iSRAccessMode::MODE_RWB)
    {
        if ($resource !== null && !is_string($resource))
            throw new \InvalidArgumentException(sprintf(
                'Temporary Stream Can Get Only The String as default prepared data. given: "%s".'
                , \Poirot\Core\flatten($resource)
            ));

        $phpTmp  = new WrapperClient('php://temp', $openMode);
        $this->setResource($phpTmp->getConnect());

        if (is_string($resource))
            $this->write($resource);
    }
}
 