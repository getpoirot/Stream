<?php
namespace Poirot\Stream\Filter;

use Poirot\Core\AbstractOptions;
use Poirot\Core\OpenOptions;
use Poirot\Stream\Interfaces\Filter\iSUserFilter;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\SFilterManager;

abstract class AbstractFilter implements iSUserFilter
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
     * You can read passed options params
     * injected with append/prepend from
     * this value
     *
     * @var array
     */
    public $params;

    // --------------------------------------------------------------

    protected $options;

    /**
     * @var resource Buffer
     */
    protected $bufferHandle;

    /**
     * Construct
     *
     * @param null|AbstractOptions $options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->options()->from($options);
    }

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
     * @return AbstractOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = self::optionsIns();

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
     * @return AbstractOptions
     */
    static function optionsIns()
    {
        return new OpenOptions;
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
        if (!SFilterManager::has($this))
            // register filter if not exists in registry
            SFilterManager::register($this);

        $filterRes = stream_filter_append(
            $streamResource->getRHandler()
            , $this->getLabel()
            , $rwFlag
            , $this->options()->toArray()
        );

        return $filterRes;
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
        if (!SFilterManager::has($this))
            // register filter if not exists in registry
            SFilterManager::register($this);

        $filterRes = stream_filter_prepend(
            $streamResource->getRHandler()
            , $this->getLabel()
            , $rwFlag
            , $this->options()->toArray()
        );

        return $filterRes;
    }

    /**
     * Filter Stream Through Buckets
     *
     * @param resource $in     userfilter.bucket brigade
     *                         pointer to a group of buckets objects containing the data to be filtered
     * @param resource $out    userfilter.bucket brigade
     *                         pointer to another group of buckets for storing the converted data
     * @param int $consumed    counter passed by reference that must be incremented by the length
     *                         of converted data
     * @param boolean $closing flag that is set to TRUE if we are in the last cycle and the stream is
     *                           about to close
     * @return int
     */
    abstract function filter($in, $out, &$consumed, $closing);


    /**
     * Read Data From Bucket
     *
     * @param resource $in       userfilter.bucket brigade
     * @param int      $consumed
     * @return string
     */
    protected function __getDataFromBucket($in, &$consumed)
    {
        $data = '';
        while ($bucket = stream_bucket_make_writeable($in)) {
            $data .= $bucket->data;
            $consumed += $bucket->datalen;
        }

        return $data;
    }

    /**
     * Write Back Filtered Data On Out Bucket
     *
     * @param resource $out  userfilter.bucket brigade
     * @param string   $data
     *
     * @return int PSFS_ERR_FATAL|PSFS_PASS_ON
     */
    protected function __writeBackDataOut($out, $data)
    {
        $buck = stream_bucket_new($this->bufferHandle, '');
        if (false === $buck)
            // trigger filter error
            return PSFS_ERR_FATAL;

        $buck->data = $data;
        stream_bucket_append($out, $buck);

        // data was processed successfully
        return PSFS_PASS_ON;
    }

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
 