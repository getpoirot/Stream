<?php
namespace Poirot\Stream\Streamable;

use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\Resource\AccessMode;
use Poirot\Stream\Streamable;
use Poirot\Stream\StreamWrapperClient;

class STemporary 
    extends Streamable
{
    /** @see http://php.net/manual/en/wrappers.php.php */
    const PHP_MEMORY = 'php://memory';
    const PHP_TEMP   = 'php://temp';

    /**
     * Construct
     *
     * @param null|string|iStreamable $resource
     * @param string      $io
     *
     * @throws \Exception
     */
    function __construct($resource = null, $io = self::PHP_MEMORY)
    {
        if ($resource !== null && !(is_string($resource) || $resource instanceof iStreamable))
            throw new \InvalidArgumentException(sprintf(
                'Temporary Stream Can Get Only The String Or iStreamable as default prepared data. given: "%s".'
                , \Poirot\Std\flatten($resource)
            ));

        $phpTmp  = new StreamWrapperClient('php://temp', new AccessMode('bRWB'));
        ## set resource for this streamable
        parent::__construct($phpTmp->getConnect());


        if (is_string($resource))
            $this->write($resource);
        elseif ($resource !== null)
            $resource->pipeTo($this);
    }
}
