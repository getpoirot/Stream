<?php
namespace Poirot\Stream\Interfaces\Resource;

use Poirot\Core\Interfaces\iPoirotOptions;
use Poirot\Stream\Interfaces\iSResource;

interface iSRBuilder extends iPoirotOptions
{
    /**
     * Build Stream Resource Handle With Config Files
     *
     * @param iSResource $handle
     *
     * @return $handle
     */
    function build(iSResource $handle);

    /**
     * @link http://php.net/manual/en/function.stream-set-blocking.php
     *
     * The stream will by default be opened in blocking mode.
     * You can switch it to non-blocking
     *
     * @param bool $flag
     *
     * @return $this
     */
    function setNoneBlocking($flag = true);

    /**
     * @link http://php.net/manual/en/function.stream-set-read-buffer.php
     *
     * Set read file buffering on the given stream
     *
     * @param int $buffer
     *
     * @return $this
     */
    function setReadBuffer($buffer);

    /**
     * Set character set for stream encoding
     *
     * @param string $encoding
     *
     * @return $this
     */
    function setEncoding($encoding);

    /**
     * @link http://php.net/manual/en/function.stream-set-write-buffer.php
     *
     * Sets write file buffering on the given stream
     *
     * @param int $buffer
     *
     * @return $this
     */
    function setWriteBuffer($buffer);

    /**
     * @link http://php.net/manual/en/function.stream-set-chunk-size.php
     *
     * Set the stream chunk size
     *
     * @param int $chunkSize
     *
     * @return $this
     */
    function setChunkSize($chunkSize);

    /**
     * @link http://php.net/manual/en/function.stream-set-timeout.php
     *
     * Set timeout period on a stream
     *
     * Note: This parameter only applies when not making asynchronous
     *       connection attempts.
     *
     * @param int $second
     * @param int $microSecond
     *
     * @return $this
     */
    function setTimeout($second, $microSecond = 0);
}
