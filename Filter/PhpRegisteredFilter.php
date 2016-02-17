<?php
namespace Poirot\Stream\Filter;

use Poirot\Std\Struct\AbstractOptions;
use Poirot\Std\Struct\OpenOptions;
use Poirot\Stream\Interfaces\Filter\ipSFilter;
use Poirot\Stream\Interfaces\iSResource;

/*
$socket = new StreamClient([
    'socket_uri'    => 'tcp://google.com:80',
    'time_out'      => 30,
]);

$resource = $socket->getConnect();
$resource->appendFilter(new PhpRegisteredFilter('zlib.inflate'), STREAM_FILTER_READ);

$stream   = new Streamable($resource);
*/

/**
 * !! If you just add an zlib.inflate filter to a stream, it's not going to work.
 * You have to skip the first two bytes before attaching the filter.
 */

class PhpRegisteredFilter implements ipSFilter
{
    protected $label;

    protected $options;

    /**
     * Construct
     *
     * @param string               $filtername zlib.*, ....
     * @param null|AbstractOptions $options
     */
    function __construct($filtername, $options = null)
    {
        $this->label = $filtername;

        if ($options !== null)
            $this->inOptions()->from($options);
    }

    /**
     * Label Used To Register Our Filter
     *
     * @return string
     */
    function getLabel()
    {
        return $this->label;
    }

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
     * @param int $rwFlag
     *
     * @return resource
     */
    function appendTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL)
    {
        $resource = $streamResource->getRHandler();
        $resource = stream_filter_append($resource, $this->getLabel(), $rwFlag, $this->inOptions()->toArray());

        return $resource;
    }

    /**
     * Attach a filter to a stream
     *
     * @param iSResource $streamResource
     * @param int $rwFlag
     *
     * @return resource
     */
    function prependTo(iSResource $streamResource, $rwFlag = STREAM_FILTER_ALL)
    {
        $resource = $streamResource->getRHandler();
        $resource = stream_filter_prepend($resource, $this->getLabel(), $rwFlag, $this->inOptions()->toArray());

        return $resource;
    }


    // ...

    /**
     * @return OpenOptions
     */
    function inOptions()
    {
        if (!$this->options)
            $this->options = self::newOptions();

        return $this->options;
    }

    /**
     * Get An Bare Options Instance
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @param null|mixed $builder Builder Options as Constructor
     *
     * @return OpenOptions
     */
    static function newOptions($builder = null)
    {
        return new OpenOptions($builder);
    }
}