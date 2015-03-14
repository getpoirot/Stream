<?php
namespace Poirot\Stream\Context;

use Poirot\Core;
use Poirot\Core\AbstractOptions;
use Poirot\Core\Interfaces\iOptionImplement;
use Poirot\Core\Interfaces\iPoirotOptions;
use Poirot\Core\Interfaces\OptionsProviderInterface;
use Poirot\Core\OpenOptions;
use Poirot\Stream\Interfaces\Context\iSContext;

!defined('POIROT_CORE_LOADED') and include_once 'Core.php';

abstract class AbstractContext extends OpenOptions
    implements
    iSContext,
    OptionsProviderInterface
{
    protected $wrapper = null;

    /**
     * @var iOptionImplement
     */
    protected $options;

    /**
     * Used To Create Context, as php on creating streams
     * get contexts options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @return string
     */
    function getsWrapper()
    {
        return $this->wrapper;
    }

    /**
     * Set/Retrieves specific options
     *
     * @return OpenOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = self::optionsIns();

        return $this->options;
    }

    /**
     * Get An Bare Options Instance
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @return AbstractOptions
     */
    static function optionsIns()
    {
        return new OpenOptions;
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
     * Set Options From Array
     *
     * @param array $options Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $options)
    {
        if (array_values($options) == $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        $wrapper = $this->getsWrapper();
        if (isset($options[$wrapper]))
            $this->options()->fromArray($options[$wrapper]);

        // set params:
        unset($options[$wrapper]);
        parent::fromArray($options);

        return $this;
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
        if (isset($params['options'])) {
            // set wrapper from resource
            $wrapper = current(array_keys($params['options']));

            $this->wrapper = $wrapper;
        }

        $this->fromArray($params);

        return $this;
    }

    /**
     * Get Properties as array
     *
     * @throws \Exception
     * @return array
     */
    function toArray()
    {
        if (!$this->wrapper)
            throw new \Exception('No Wrapper Defined Yet!!');

        $params  = parent::toArray();
        $options = [
            "{$this->getsWrapper()}" => $this->options()->toArray()
        ];

        return Core\array_merge($params, $options);
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
            "{$this->getsWrapper()}" => $this->options()->toArray()
        ];

        return stream_context_create($options, parent::toArray());
    }
}
