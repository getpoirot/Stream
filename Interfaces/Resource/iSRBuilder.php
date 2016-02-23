<?php
namespace Poirot\Stream\Interfaces\Resource;

use Poirot\Std\Interfaces\Struct\iOptionsData;
use Poirot\Stream\Interfaces\Filter\iSFilter;
use Poirot\Stream\Interfaces\iSResource;

interface iSRBuilder extends iOptionsData
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
     * Set Filters
     *
     * @param [iSFilter] $filters
     *
     * @return $this
     */
    function setFilters($filters);

    /**
     * Set Filter
     *
     * - append filter to resource
     *
     * @param iSFilter $filter
     *
     * @return $this
     */
    function setFilter(iSFilter $filter);

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
     * ! When the stream times out,
     *   the 'timed_out' key of the array returned
     *   by stream_get_meta_data() is set to TRUE,
     *   although no error/warning is generated.
     *
     * Note: This parameter only applies when not making asynchronous
     *       connection attempts
     *
     * Note: To set a timeout for reading/writing data over the socket,
     *       use the stream_set_timeout(), as the timeout only applies
     *       while making connecting the socket
     *
     * Note: This function doesn't work with advanced operations like
     *       stream_socket_recvfrom(), use stream_select() with timeout
     *       parameter instead
     *
     * @param int $seconds      The seconds part of the timeout to be set
     * @param int $microseconds The microseconds part of the timeout to be set
     *
     * @return $this
     */
    function setTimeout($seconds, $microseconds = 0);
}
