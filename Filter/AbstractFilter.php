<?php
namespace Poirot\Stream\Filter;

use Poirot\Stream\Interfaces\Filter\iSFilter;
use Poirot\Stream\Interfaces\iSResource;

abstract class AbstractFilter implements iSFilter
{
    /**
    * filter name passed to class
    *
    * stream_filter_register('template.*', 'Address\To\ThisClass' ..
    * file_put_contents('php://filter/write=template.some_name' ....
    * now filtername is template.some_name
    *
    * @var string
    */
    public $filtername;

    /**
     * @var resource Buffer
     */
    protected $bufferHandle;

    /**
     * Label Used To Register Our Filter
     *
     * @return string
     */
    function getLabel()
    {
        if ($this->filtername)
            return $this->filtername;

        $className = explode('\\', get_class($this));
        $className = $className[count($className)-1];

        return $className.'.*';
    }

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
    abstract function filter($in, $out, &$consumed, $closing);


    /**
     * called respectively when our class is created
     */
    function onCreate()
    {
        $this->bufferHandle = @fopen('php://temp', 'w+');
        if (false !== $this->bufferHandle)
            return true;

        return false;
    }

    /**
     * called respectively when our class is destroyed
     */
    function onClose()
    {
        @fclose($this->bufferHandle);
    }
}
 