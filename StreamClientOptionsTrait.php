<?php
namespace Poirot\Stream;

use Poirot\Stream\Context\Socket\SocketContext;
use Poirot\Stream\Interfaces\Context\iSContext;

trait StreamClientOptionsTrait
{
    /** @var string */
    protected $socketUri    = null;

    // default options

    /** @var float */
    protected $timeout      = 20;
    /** @var boolean */
    protected $persist      = false;
    /** @var boolean */
    protected $noneBlocking = false;
    /** @var boolean */
    protected $async        = false;
    /** @var iSContext */
    protected $context;

    /**
     * Set Socket Uri
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1),
     *       you must enclose the IP in square brackets—for example,
     *       tcp://[fe80::1]:80
     *
     * @param string $socketUri
     *
     * @return $this
     */
    function setSocketUri($socketUri)
    {
        $this->socketUri = $socketUri;
        return $this;
    }

    /**
     * Get Current Socket Uri That Stream Built With
     *
     * @return string
     */
    function getSocketUri()
    {
        return $this->socketUri;
    }

    /**
     * Set Default Base Context Options
     *
     * @param iSContext|array|resource $context
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function setContext($context)
    {
        if ($context instanceof iSContext)
            $this->context = $context;
        else
            $this->getContext()->from($context);

        return $this;
    }

    /**
     * Get Default Base Context Options
     *
     * @return iSContext
     */
    function getContext()
    {
        if (!$this->context)
            $this->setContext(new SocketContext);

        return $this->context;
    }

    /**
     * Set timeout period on a stream
     *
     * @see iSResource::setTimeout
     *
     * @param float $seconds In Form Of 5.3
     *
     * @return $this
     */
    function setTimeout($seconds)
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Get Timeout
     *
     * @return array[$second, $microsecond]
     */
    function getTimeout()
    {
        if (!$this->timeout)
            $this->timeout = ini_get('default_socket_timeout');

        return $this->timeout;
    }

    /**
     * Set To Persistent Internet or Unix Domain Socket
     * Connection Built
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setPersist($flag = true)
    {
        $this->persist = (boolean) $flag;
        return $this;
    }

    /**
     * Indicate Is Connection Have To Built On Persistent Mode
     *
     * @return boolean
     */
    function isPersist()
    {
        return $this->persist;
    }

    /**
     * Set blocking/non-blocking mode on a stream
     *
     * ! This function works for any stream that supports
     *   non-blocking mode (currently, regular files and socket streams)
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setNoneBlocking($flag = true)
    {
        $this->noneBlocking = (boolean) $flag;
        return $this;
    }

    /**
     * Indicate Where Stream Is Built With None-Blocking Mode?
     *
     * @return boolean
     */
    function isNoneBlocking()
    {
        return $this->noneBlocking;
    }

    /**
     * @param boolean $async
     * @return $this
     */
    function setAsync($async = true)
    {
        $this->async = (boolean) $async;
        return $this;
    }

    /**
     * @return boolean
     */
    function isAsync()
    {
        return $this->async;
    }
}
