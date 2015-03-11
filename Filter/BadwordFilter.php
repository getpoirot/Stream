<?php
namespace Poirot\Stream\Filter;

use Poirot\Stream\Interfaces\iSResource;

class BadwordFilter extends AbstractFilter
{
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
     * @param int $rwFlag
     *
     * @return $this
     */
    function appendTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL)
    {
        // TODO: Implement appendTo() method.
    }

    /**
     * Attach a filter to a stream
     *
     * @param iSResource $streamResource
     * @param int $rwFlag
     *
     * @return $this
     */
    function prependTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL)
    {
        // TODO: Implement prependTo() method.
    }

    /**
     * @param $in       pointer to a group of buckets objects containing the data to be filtered
     * @param $out      pointer to another group of buckets for storing the converted data
     * @param $consumed counter passed by reference that must be incremented by the length of converted data
     * @param $closing  boolean flag that is set to TRUE if we are in the last cycle and the stream is about to close
     */
    function filter($in, $out, &$consumed, $closing)
    {
        $data = '';

        while ($bucket = stream_bucket_make_writeable($in)) {
            $data .= $bucket->data;
            $consumed += $bucket->datalen;
        }

        $buck = stream_bucket_new($this->bufferHandle, '');

        if (false === $buck)
            // trigger filter error
            return PSFS_ERR_FATAL;

        #$parser = new MarkdownExtra;
        #$html = $parser->transform($data);
        $html  = $data;
        $buck->data = $html;

        stream_bucket_append($out, $buck);

        // data was processed successfully
        return PSFS_PASS_ON;
    }
}
 