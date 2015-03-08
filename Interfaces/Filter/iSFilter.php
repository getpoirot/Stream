<?php
namespace Poirot\Stream\Interfaces\Filter;

use Poirot\Stream\Interfaces\iSResource;

/**
 * stream_filter_register() must be called first in order
 * to register the desired user filter to filtername.
 *
 * Using iSFManager To Register Filters
 */
interface iSFilter
{
    /*
    php_user_filter prototype
    */

    # public $filtername;
    # public $params;

    /**
     * @link http://php.net/manual/en/function.stream-filter-append.php
     *
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

    /*
    php_user_filter prototype
    */

    /**
     * @param $in
     * @param $out
     * @param $consumed
     * @param $closing
     */
    function filter ($in, $out, &$consumed, $closing);

    function onCreate ();

    function onClose ();
}
