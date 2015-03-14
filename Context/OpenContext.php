<?php
namespace Poirot\Stream\Context;

use Poirot\Core\Interfaces\iOptionImplement;
use Poirot\Core\Interfaces\iPoirotOptions;
use Poirot\Core\OpenOptions;
use Poirot\Core\Traits\OpenOptionsTrait;
use Poirot\Stream\Interfaces\Context\iSContext;
use Poirot\Stream\SWrapperManager;

class OpenContext
    implements iSContext
{
    use OpenOptionsTrait;

    /**
     * @var OpenOptions
     */
    protected $params;

    /**
     * @var string Wrapper Name
     */
    public $wrapper;

    /**
     * Construct
     *
     *  wrapper options:
     * 'http'=>array(
     *  'method'=>"GET",
     *  'header'=>"Accept-language: en\r\n" .
     *  "Cookie: foo=bar\r\n"
     *  )
     *
     * @param array|iOptionImplement|resource $options Options
     * @param null|string  $wrapper
     */
    function __construct($options = null, $wrapper = null)
    {
        if (count($options) === 1 && $wrapper === null) {
            // In Case of wrapper options:
            $w = current(array_keys($options));

            if (SWrapperManager::isRegistered($w)) {
                $wrapper = $w;
                $options = $options[$wrapper];
            }
        }

        $this->wrapper = $wrapper;

        if ($options !== null)
            $this->from($options);
    }

    /**
     * Used To Create Context, as php on creating streams
     * get contexts options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @param string $wrapper
     *
     * @return $this
     */
    function forWrapper($wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    protected function __setWrapperFromContext($resource)
    {
        $params = stream_context_get_params($resource);

        if (isset($params['options'])) {
            $wrapper = current(array_keys($params['options']));

            $this->forWrapper($wrapper);
        }
    }

    /**
     * Set Options
     *
     * @param array|iPoirotOptions|resource $options
     *
     * @return $this
     */
    function from($options)
    {
        if (is_array($options))
            $this->fromArray($options);
        elseif (is_resource($options))
            $this->fromContext($options);
        elseif ($options instanceof iPoirotOptions)
            $this->fromOption($options);

        return $this;
    }

    /**
     * Set/Retrieves parameters
     *
     * - data params used on $this::toContext
     *   to set params of context
     *
     * @return OpenOptions
     */
    function params()
    {
        if (!$this->params)
            $this->params = new OpenOptions;

        return $this->params;
    }

    /**
     * Set Options From Context Resource
     *
     * - get parameters from context and store on object
     *   by $this::params
     * - rewrite wrapper with resource wrapper name
     *
     * @param resource $resource Context/Stream
     * @return $this
     */
    function fromContext($resource)
    {
        if (!is_resource($resource))
            throw new \InvalidArgumentException(sprintf(
                'Invalid Resource As Argument, given: "%s".'
                , is_object($resource) ? get_class($resource) : gettype($resource)
            ));

        $params = stream_context_get_params($resource);

        // set options:
        $this->__setWrapperFromContext($resource);

        $wrapper = $this->wrapper;
        if (isset($params['options']) && isset($params['options'][$wrapper]))
            $this->fromArray($params['options'][$wrapper]);

        // set params:
        unset($params['options']);
        $this->params()->fromArray($params);

        return $this;
    }

    /**
     * Creates and returns a stream context with any
     * options supplied in options preset
     *
     * - Set Parameters On Context
     *   parameters are accessible by $this::params
     *   method.
     *
     * @throws \Exception not wrapper defined
     * @return resource
     */
    function toContext()
    {
        if (!$this->wrapper)
            throw new \Exception('No Wrapper Defined Yet!!');

        $options = [
            $this->wrapper => $this->toArray()
        ];

        return stream_context_create($options, $this->params()->toArray());
    }
}
 