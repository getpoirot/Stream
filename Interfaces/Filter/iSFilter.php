<?php
namespace Poirot\Stream\Interfaces\Filter;

use Poirot\Core\Interfaces\OptionsProviderInterface;
use Poirot\Stream\Interfaces\iSResource;

/**
 * stream_filter_register() must be called first in order
 * to register the desired user filter to filtername.
 *
 * Using iSFManager To Register Filters
 *
 * Filters Manipulate Every Chunk Of Data That Read/Write
 * Separately on each action
 *
 */
interface iSFilter extends OptionsProviderInterface
{
    /**
     * Filter processed successfully with data available in the out bucket
     * brigade.
     *
     * @const int
     */
    const PASS_ON          = PSFS_PASS_ON;

    /**
     * Filter processed successfully, however no data was available to return.
     * More data is required from the stream or prior filter.
     *
     * @const int
     */
    const FEED_ME          = PSFS_FEED_ME;

    /**
     * The filter experienced and unrecoverable error and cannot continue.
     *
     * @const int
     */
    const FATAL_ERROR      = PSFS_ERR_FATAL;

    /**
     * Regular read/write.
     *
     * @const int
     */
    const FLAG_NORMAL      = PSFS_FLAG_NORMAL;

    /**
     * An incremental flush.
     *
     * @const int
     */
    const FLAG_FLUSH_INC   = PSFS_FLAG_FLUSH_INC;

    /**
     * Final flush prior to closing.
     *
     * @const int
     */
    const FLAG_FLUSH_CLOSE = PSFS_FLAG_FLUSH_CLOSE;

    /*
    php_user_filter prototype
    */

    # public $filtername;
    # public $params;

    /**
     * Label Used To Register Our Filter
     *
     * @return string
     */
    function getLabel();

    /**
     * Append Filter To Resource Stream
     *
     * ! By default, stream_filter_append() will attach the filter
     *   to the read filter chain if the file was opened for reading
     *   (i.e. File Mode: r, and/or +). The filter will also be attached
     *   to the write filter chain if the file was opened for writing
     *   (i.e. File Mode: w, a, and/or +). STREAM_FILTER_READ, STREAM_FILTER_WRITE,
     *   and/or STREAM_FILTER_ALL can also be passed to the read_write parameter to
     *   override this behavior.
     *
     * Note: Stream data is read from resources (both local and remote) in chunks,
     *       with any unconsumed data kept in internal buffers. When a new filter
     *       is appended to a stream, data in the internal buffers is processed through
     *       the new filter at that time. This differs from the behavior of
     *       stream_filter_prepend()
     *
     * Note: When a filter is added for read and write, two instances of the filter are created.
     *       stream_filter_append() must be called twice with STREAM_FILTER_READ and STREAM_FILTER_WRITE
     *       to get both filter resources.
     *
     * @param iSResource $streamResource
     * @param int        $rwFlag
     *
     * @return $this
     */
    function appendTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL);

    /**
     * Attach a filter to a stream
     *
     * @param iSResource $streamResource
     * @param int        $rwFlag
     *
     * @return $this
     */
    function prependTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL);
}
