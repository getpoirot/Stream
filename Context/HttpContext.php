<?php
namespace Poirot\Stream\Context;

/**
 * Context options for http:// and https:// transports
 *
 */
class HttpContext extends AbstractContext
{
    protected $wrapper = 'http';

    // Options
    /** @var string */
    protected $method = 'GET';
    /** @var array|string */
    protected $header;
    /** @var string By default the user_agent php.ini setting is used */
    protected $userAgent;
    /** @var string */
    protected $content;
    /** @var string */
    protected $proxy;
    /** @var boolean */
    protected $requestFulluri = false;
    /** @var int */
    protected $followLocation = 1;
    /** @var int */
    protected $maxRedirects = 20;
    /** @var float */
    protected $protocolVersion = 1.0;
    /** @var float By default the default_socket_timeout php.ini setting is used */
    protected $timeout;
    /** @var boolean */
    protected $ignoreErrors = false;


    protected function __init()
    {
        // Bind Socket Context
        $this->bindWith(new SocketContext);
    }


    /**
     * GET, POST, or any other HTTP method supported by the remote server
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Additional headers to be sent during request.
     * Values in this option will override other values
     * (such as User-agent:, Host:, and Authentication:)
     *
     * @param array|string $header
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Value to send with User-Agent: header.
     * This value will only be used if user-agent
     * is not specified in the header context option above
     *
     * @param string $userAgent
     *
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        if (!$this->userAgent)
            $this->setUserAgent(ini_get('user_agent'));

        return $this->userAgent;
    }

    /**
     * Additional data to be sent after the headers.
     * Typically used with POST or PUT requests
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * URI specifying address of proxy server.
     * (e.g. tcp://proxy.example.com:5100)
     *
     * @param string $proxy
     *
     * @return $this
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * When set to TRUE, the entire URI will be used when
     * constructing the request.
     * (i.e. GET http://www.example.com/path/to/file.html HTTP/1.0).
     * While this is a non-standard request format, some proxy
     * servers require it
     *
     * @param boolean $requestFulluri
     *
     * @return $this
     */
    public function setRequestFulluri($requestFulluri)
    {
        $this->requestFulluri = $requestFulluri;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRequestFulluri()
    {
        return $this->requestFulluri;
    }

    /**
     * Follow Location header redirects. Set to 0 to disable
     *
     * @param int $followLocation
     *
     * @return $this
     */
    public function setFollowLocation($followLocation)
    {
        $this->followLocation = $followLocation;
        return $this;
    }

    /**
     * @return int
     */
    public function getFollowLocation()
    {
        return $this->followLocation;
    }

    /**
     * The max number of redirects to follow.
     * Value 1 or less means that no redirects are followed
     *
     * @param int $maxRedirects
     *
     * @return $this
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * HTTP protocol version
     *
     * Note: PHP prior to 5.3.0 does not implement chunked
     *       transfer decoding. If this value is set to 1.1 it
     *       is your responsibility to be 1.1 compliant
     *
     * @param float $protocolVersion
     *
     * @return $this
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    /**
     * @return float
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Read timeout in seconds, specified by a float (e.g. 10.5)
     *
     * @param float $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeout()
    {
        if ($this->timeout == null)
            $this->setTimeout(ini_get('default_socket_timeout'));

        return $this->timeout;
    }

    /**
     * Fetch the content even on failure status codes
     *
     * @param boolean $ignoreErrors
     *
     * @return $this
     */
    public function setIgnoreErrors($ignoreErrors)
    {
        $this->ignoreErrors = $ignoreErrors;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreErrors()
    {
        return $this->ignoreErrors;
    }
}
