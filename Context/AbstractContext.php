<?php
namespace Poirot\Stream\Context;

use Poirot\Std;
use Poirot\Std\Interfaces\Struct\iOptionsData;
use Poirot\Stream\Interfaces\Context\iSContext;
use Traversable;

// TODO where is functions ?
!defined('POIROT_CORE_LOADED') and include_once 'functions.php';

class AbstractContext extends Std\Struct\OpenOptionsData
    implements iSContext
{
    protected $wrapper = null;

    // params

    /**
     * @var callable
     */
    protected $notification;

    // options (bind contexts)

    /**
     * @var array[AbstractContext]
     */
    protected $bindContexts = [];

    /**
     * Construct
     *
     * - params in form of get_context_params
     *   [ 'notification' => ...
     *     'options'|'bind_with' => $contextOptions
     *
     * @param array|iOptionsData $contextParams Options
     */
    function __construct($contextParams = null)
    {
        $this->__init();
        parent::__construct($contextParams);
    }

    /**
     * Called by __construct
     */
    protected function __init() { }

    /**
     * Do Set Data From
     * @param array|\Traversable $data
     */
    protected function doSetFrom($data)
    {
        // TODO get available options from this context for understanding option key for set or binding context

        if ($data instanceof \Traversable)
            $data = \Poirot\Std\iterator_to_array($data);

        if (isset($data['bind_with'])) {
            $data['options'] = $data['bind_with'];
            unset($data['bind_with']);
        }

        if (isset($data['options'])) {
            // get_context_options ['wrapper_name'=>.., ..]
            foreach ($data['options'] as $b => $v) $this->bindWith(new BaseContext($b, $v));
            unset($data['options']);
        }

        // get_context_params ['param_name'=>.., 'wrapper_name'=>.., ..]
        foreach($data as $name => $opts) {
            $name = (string) $name;
            // Set Context Param: notification
            $this->__set($name, $opts);
        }
    }

    /**
     * Used To Create Context, as php on creating streams
     * contexts get options as associative array with
     * $arr['wrapper']['option'] = $value format
     *
     * @throws \Exception
     * @return string
     */
    function wrapperName()
    {
        $wrapper = $this->wrapper;
        if ($wrapper === null)
            throw new \Exception(sprintf(
                'No Wrapper Defined on (%s) Context.'
                , Std\flatten($this)
            ));

        return $wrapper;
    }

    /**
     * Bind Another Context Along this
     *
     * [
     *   'socket' => [
     *     // socket context options
     *     ...
     *   ],
     *   'http' => [
     *     // http context options
     *     ...
     *   ]
     * ]
     *
     * @param iSContext $context
     *
     * @return $this
     */
    function bindWith(iSContext $context)
    {
        $this->bindContexts[strtolower($context->wrapperName())] = $context;
        return $this;
    }

    /**
     * Context with specific wrapper has bind?
     *
     * @param string $wrapperName
     *
     * @return false|iSContext
     */
    function hasBind($wrapperName)
    {
        $normalized = strtolower($wrapperName);
        return (array_key_exists($normalized, $this->bindContexts))
            ? $this->bindContexts[$normalized]
            : false;
    }

    /**
     * List of Wrapper Name Of Currently Bind Contexts
     *
     * @return array[ (string) wrapperName ]
     */
    function listBindContexts()
    {
        return array_keys($this->bindContexts);
    }

    // Default PHP Context Params:

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
        if ($notification !== null && !is_callable($notification))
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
     * @ignore
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        foreach(parent::getIterator() as $key => $val) {
            if ($val === null) continue;
            yield (string) $key => $val;
        }

        // Bind Contexts:
        $binds = [];
        foreach($this->listBindContexts() as $context)
        {
            // TODO each bind context may have options => [] (bind_with) inside
            // but here we just used context specific params,
            $contextParams = \Poirot\Std\iterator_to_array($this->hasBind($context), function($key, $val) {
                ### we don`t want null values on context params
                return ($val === null);
            });

            if (isset($contextParams['options'])) unset($contextParams['options']);
            $binds[$context] = $contextParams;
        }

        if (!empty($binds))
            yield 'options' => $binds;
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
        $params  = \Poirot\Std\iterator_to_array($this);
        $options = $params['options'];
        unset($params['options']);

        return stream_context_create($options, $params);
    }


    // ..

    /**
     * access context options bind to this context
     *
     * $cntx->setSocket(['bindTo' => ..])
     * $cntx->setHttp(['connection' => ..])
     *
     * $cntx->socket()->setBindTo(..)
     * $cntx->http()->setConnection(...)
     */
    function __call($method, $args)
    {
        ## method setSocket(...)
        if (strpos($method, 'set') === 0) {
            $method = substr($method, -(strlen($method)-strlen('set')));
            $setterCall = true;
        }

        if ($context = $this->hasBind($method)) {
            if (isset($setterCall)) {
                // $cntx->setSocket(['bindTo' => ..])
                $context->from($args[0]);
                return $this;
            }

            // $cntx->socket()->setBindTo(..)
            return $context;
        }


        // ...

        $debugTrace = debug_backtrace();
        // TODO test debug backtrace existance
        throw new \ErrorException(sprintf(
            'Call to undefined method (%s).'
            , $method
        ), 0, 1, $debugTrace[1]['file'], $debugTrace[1]['line']);
    }
}
