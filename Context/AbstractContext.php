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

abstract class AbstractContext extends AbstractOptions
    implements
    iSContext,
    OptionsProviderInterface
{
    protected $wrapper = null;

    // params

    /**
     * @var callable
     */
    protected $notification;

    // options

    /**
     * @var iOptionImplement
     */
    protected $options;

    /**
     * @var array[AbstractContext]
     */
    protected $bindContexts = [];

    /**
     * Construct
     *
     * @param array|iPoirotOptions $options Options
     */
    function __construct($options = null)
    {
        $this->__before_construct();

        parent::__construct($options);
    }

    /**
     * Called by __construct
     */
    protected function __before_construct()
    {

    }

    /**
     * Used To Create Context, as php on creating streams
     * contexts get options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @throws \Exception
     * @return string
     */
    function _getWrapper()
    {
        $wrapper = $this->wrapper;
        if ($wrapper === null)
            throw new \Exception('No Wrapper Defined Yet!!');

        return $wrapper;
    }

    /**
     * Bind Another Context With this
     *
     * [
     * * 'socket' => [
            'bindto' => '192.168.0.100:7000',
        ],
     * * 'http' => [
     *      ...
     *  ]
     * ]
     *
     * @param AbstractContext $context
     *
     * @return $this
     */
    function bindContext(AbstractContext $context)
    {
        $this->bindContexts[] = $context;

        return $this;
    }

    // Params:

    /**
     * Set callback function for the notification context parameter
     * @link http://php.net/manual/en/function.stream-notification-callback.php
     *
     * @param callable $notification
     *
     * @return $this
     */
    public function setNotification($notification)
    {
        if (!is_callable($notification))
            throw new \InvalidArgumentException('Notification handler must be a callable.');

        $this->notification = $notification;

        return $this;
    }

    /**
     * @return callable
     */
    public function getNotification()
    {
        return $this->notification;
    }

    // Context:

    /**
     * Set/Retrieves specific options
     *
     * @return OpenOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = static::optionsIns();

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
     * @return OpenOptions
     */
    static function optionsIns()
    {
        return new OpenOptions;
    }

    /**
     * access context options bind to this context
     *
     * $cntx->socket->setBindTo(..)
     * $cntx->http->setConnection(...)
     *
     */
    function __call($method, $args)
    {
        $bindContexts = [$this];
        $bindContexts = array_merge($bindContexts, $this->bindContexts);

        while ($context = array_shift($bindContexts)) {
            $wrapper = $context->_getWrapper();
            if ($method === $wrapper)
                return $context;
        }

        throw new \Exception('Method "'. $method. '" Is Unknown.');
    }

    /**
     * Set Options
     *
     * ! called by __construct
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
            $this->fromResource($options);
        elseif ($options instanceof iPoirotOptions)
            $this->fromSimilar($options);

        return $this;
    }

    /**
     * Set Options From Array
     *
     * $opts = array(
     *    'notification' => 'dfsf',
     *    'options' => [
     *       'socket' => array(
     *          'bindto'  => '192.168.0.100:0',
     *        ),
     *     ]
     * );
     *
     * or
     *
     * $opts = array(
     *   'notification' => 'dfsf',
     *   'socket' => array(
     *      'bindto'  => '192.168.0.100:0',
     *   ),
     * );
     *
     * @param array $params Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $params)
    {
        if (!empty($params) && array_values($params) === $params)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        $opts = $params;
        if (isset($params['options'])) {
            // in case of context params, that include options key
            // 'options => [
            //    'socket' => array(
            //       'bindto'  => '192.168.0.100:0',
            //     ),
            $opts = $params['options'];
            unset($params['options']);
        }

        $bindContexts = [$this];
        $bindContexts = array_merge($bindContexts, $this->bindContexts);

        while ($context = array_shift($bindContexts)) {
            $wrapper = $context->_getWrapper();
            if (isset($opts[$wrapper])) {
                $context->options()->fromArray($opts[$wrapper]);
                unset($params[$wrapper]);
            }
        }

        // set params:
        parent::fromArray($params);

        return $this;
    }

    /**
     * Set Options From Same Option Object
     *
     * note: it will take an option object instance of $this
     *       OpenOptions only take OpenOptions as argument
     *
     * - also you can check for private and write_only
     *   methods inside Options Object to get fully coincident copy
     *   of Options Class Object
     *
     * @param iSContext $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromSimilar(/*iSContext*/ $options)
    {
        $return = parent::fromSimilar($options);

        // assimilate options
        $this->options()->fromSimilar($options->options());

        // bind contexts
        foreach($options->bindContexts as $context)
        $this->bindContext($context);

        return $return;
    }

    /**
     * Set Options From Context Resource
     *
     * - get parameters from context and store on object
     *   by $this::params
     * - rewrite wrapper with resource wrapper name
     *
     * @param resource $resource Context/Stream
     *
     * @throws \Exception
     * @return $this
     */
    function fromResource($resource)
    {
        if (!is_resource($resource) && get_resource_type($resource) !== 'stream-context')
            throw new \InvalidArgumentException(sprintf(
                'Invalid Context Resource Passed, given: "%s".'
                , \Poirot\Core\flatten($resource)
            ));

        $params = stream_context_get_params($resource);
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
        $bindContexts = [$this];
        $bindContexts = array_merge($bindContexts, $this->bindContexts);

        $options = [];
        /** @var AbstractContext $context */
        while ($context = array_shift($bindContexts)) {
            $wrapper = $context->_getWrapper();
            $options['options'][$wrapper] = $context->options()->toArray();

            $ops = &$options['options'][$wrapper];
            foreach ($ops as $key => &$p) {
                // cleanup null values for context params
                if ($p === null)
                    unset($ops[$key]);
            }
        }

        $params  = parent::toArray();
        foreach ($params as $key => $v) {
            // cleanup null values for context params
            if ($v === null)
                unset($params[$key]);
        }

        $result = Core\array_merge($params, $options);
        return $result;
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
        $params  = $this->toArray();

        $options = $params['options'];
        unset($params['options']);

        return stream_context_create($options, $params);
    }
}
