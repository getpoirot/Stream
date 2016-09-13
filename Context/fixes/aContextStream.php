<?php
namespace Poirot\Stream\Context;

use Poirot\Std\Struct\aDataOptions\PropObject;
use Traversable;

use Poirot\Std;

use Poirot\Stream\Interfaces\Context\iContextStream;

/*
$socket = new SocketContext([
    'notification' => function() {},
    'options' => [
        'http' => [ // bind context
            'method' => 'POST'
        ],
        'socket' => [
            'bindto' => ':7000'
        ],
    ],
]);
*/

class aContextStream
    extends Std\Struct\DataOptionsOpen
    implements iContextStream
{
    protected $wrapper = null;

    /** @var callable */
    protected $notification;

    /** @var aContextStream[] */
    protected $bindContexts = array();

    /**
     * Construct
     *
     * - params in form of get_context_params
     *   [ 'notification' => ...params
     *     'options'|'bind_with' => [ $contextOptions..
     *
     * @param null|array|\Traversable $contextParams Options
     */
    function __construct($contextParams = null)
    {
        $this->_init();
        parent::__construct($contextParams);
    }

    /**
     * Called by __construct
     */
    protected function _init() { }

    /**
     * Do Set Data From
     * @param array|\Traversable $data
     */
    protected function doSetFrom($data)
    {
        if ($data instanceof \Traversable)
            $data = Std\cast($data)->toArray();

        if (isset($data['bind_with'])) {
            (isset($data['options'])) 
                ? $data['options'] = array_merge($data['options'], $data['bind_with'])
                : $data['options'] = $data['bind_with']
            ;
            unset($data['bind_with']);
        }

        if (isset($data['options'])) {
            // get_context_options ['wrapper_name'=>.., ..]
            foreach ($data['options'] as $b => $v) 
                $this->bindWith(new ContextStreamBase($b, $v));
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
     * Wrapper name
     * @ignore
     * 
     * @throws \Exception
     * @return string
     */
    function getWrapperName()
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
     * @param iContextStream $context
     *
     * @return $this
     */
    function bindWith(iContextStream $context)
    {
        $wrapperName = strtolower($context->getWrapperName());
        $this->bindContexts[$wrapperName] = $context;
        return $this;
    }

    /**
     * Context with specific wrapper has bind?
     *
     * @param string $wrapperName
     *
     * @return iContextStream|false
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
    
//    protected function _getIterator()
//    {
//        /** @var PropObject $p */
//        foreach($this->_getProperties() as $p) {
//            if (!$p->isReadable()) continue;
//
//            $val = $this->__get($p->getKey());
//            yield (string) $p => $val;
//        }
//
//        if ($binds = $this->_getBindContextOptions())
//            yield 'options' => $binds;
//    }

    // DO_LEAST_PHPVER_SUPPORT v5.5 yeild
    protected function _fix__getIterator()
    {
        $arr = array();
        foreach($this->_getProperties() as $p) {
            if (!$p->isReadable()) continue;

            $val = $this->__get($p->getKey());
            $arr[(string) $p] = $val;
        }

        if ($binds = $this->_getBindContextOptions())
            $arr['options'] = $binds;
        
        return new \ArrayIterator($arr);
    }
    
    protected function _getBindContextOptions()
    {
        // Bind Contexts:
        $binds = array();
        foreach($this->listBindContexts() as $context)
        {
            // TODO each bind context may have options => [] (bind_with) inside
            // but here we just used context specific params,
            $contextParams = new Std\Type\StdTravers($this->hasBind($context));
            $contextParams = $contextParams->toArray(function($key, $val) {
                ### we don`t want null values on context params
                return ($val === null);
            });

            if (isset($contextParams['options'])) unset($contextParams['options']);
            $binds[$context] = $contextParams;
        }

        return $binds;
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
        $params  = new Std\Type\StdTravers($this);
        $params  = $params->toArray();
        $options = (isset($params['options'])) ? $params['options'] : array();
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
        $origMethod = $method;
        ## method setSocket(...)
        if (strpos($method, 'set') === 0) {
            $method = substr($method, -(strlen($method)-strlen('set')));
            $setterCall = true;
        }

        if (strpos($method, 'get') === 0) {
            $method = substr($method, -(strlen($method)-strlen('set')));
            $getterCall = true;
        }

        if ($context = $this->hasBind($method)) {
            if (isset($setterCall)) {
                // $cntx->setSocket(['bindTo' => ..])
                $context->import($args[0]);
                return $this;
            }

            // $cntx->socket()->setBindTo(..)
            return $context;
        }

        if (isset($setterCall) || isset($getterCall)) {
            // $socket->Http()->setUserAgent('Firefox');
            return parent::__call($origMethod, $args);
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
