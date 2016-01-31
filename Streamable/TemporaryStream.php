<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\Resource\iSRAccessMode;
use Poirot\Stream\Resource\SROpenMode;
use Poirot\Stream\Streamable;
use Poirot\Stream\WrapperClient;

class TemporaryStream extends Streamable
{
    /**
     * Construct
     *
     * @param null|string          $resource
     */
    function __construct($resource = null)
    {
        if ($resource !== null && !is_string($resource))
            throw new \InvalidArgumentException(sprintf(
                'Temporary Stream Can Get Only The String as default prepared data. given: "%s".'
                , \Poirot\Core\flatten($resource)
            ));

        $phpTmp  = new WrapperClient('php://temp', new SROpenMode('bRWB'));
        ## set resource for this streamable
        parent::__construct($phpTmp->getConnect());

        if (is_string($resource))
            $this->write($resource);
    }
}
