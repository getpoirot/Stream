<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Stream\Wrapper\AccessControl\ACWOptions;

/**
 * Note: This is simple class just demonstrate how
 *       to implement wrapper features
 *
 * (!) The Registered Wrappers Constructed On Each fopen
 *     With Contexts
 */
class AccessControlWrapper extends AbstractWrapper
{
    /**
     * @var ACWOptions
     */
    protected $options;

    /**
     * File Handler Resource
     *
     * @var resource
     */
    protected $fp;

    /**
     * Directory Handler Resource
     *
     * @var resource
     */
    protected $dh;

    /**
     * Construct
     *
     * @param ACWOptions|array $options
     */
    function __construct($options = null)
    {
       if ($options !== null)
           $this->inOptions()->from($options);
    }

    /**
     * Get Wrapper Protocol Label
     *
     * - used on register/unregister wrappers, ...
     *
     * @return string
     */
    function getLabel()
    {
        return 'iacc';
    }

    /**
     * @return ACWOptions
     */
    function inOptions()
    {
        return parent::inOptions();
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
     * @return ACWOptions
     */
    static function newOptions($builder = null)
    {
        return new ACWOptions($builder);
    }

    // Implement Wrapper:

    protected function assertReadAccess()
    {
        if (!$this->inOptions()->getPermissions()->hasReadAccess())
            throw new \Exception('Access Was Denied On Reading.');
    }

    protected function assertWriteAccess()
    {
        if (!$this->inOptions()->getPermissions()->hasWriteAccess())
            throw new \Exception('Access Was Denied On Writing.');
    }

    /**
     * Remove Wrapper Scheme From Path
     *
     * ! remove iacc://path/to/ from begining path
     *   to avoid recursive wrapper call and faild
     *   on using filesystem functions
     *
     * @param string $path
     *
     * @return string
     */
    protected function cleanUpPath($path)
    {
        return str_replace('iacc://', '', $path);
    }

    /**
     * @param string       $path        Path passed containing wrapper, iacc://path/to/
     * @param string       $mode
     * @param int          $options
     * @param string       $opened_path
     *
     * @return bool
     */
    function stream_open($path, $mode, $options, &$opened_path)
    {
        $path = $this->cleanUpPath($path);

        $this->fp = fopen($path, $mode, $options);

        return $this->fp !== false;
    }

    /**
     * @return void
     */
    function stream_close()
    {
        fclose($this->fp);
        $this->fp = 0;

        return;
    }

    /**
     * @param int $count
     * @return string
     * @throws \Exception
     */
    function stream_read($count)
    {
        $this->assertReadAccess();

        return fread($this->fp, $count);
    }

    /**
     * @param string $data
     * @return int|mixed|string
     * @throws \Exception
     */
    function stream_write($data)
    {
        $this->assertWriteAccess();

        return fwrite($this->fp, $data);
    }

    /**
     * @return bool
     */
    function stream_eof()
    {
        return feof($this->fp);
    }

    /**
     * @return int
     */
    function stream_tell()
    {
        return ftell($this->fp);
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return bool|int
     */
    function stream_seek($offset,$whence )
    {
        return fseek($this->fp, $offset, $whence);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function stream_flush()
    {
        $this->assertWriteAccess();

        return fflush($this->fp);
    }

    /**
     * @return array
     */
    function stream_stat()
    {
        return fstat($this->fp);
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    function unlink($path)
    {
        $this->assertWriteAccess();

        return unlink($path);
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     * @throws \Exception
     */
    function rename($from,$to)
    {
        $this->assertWriteAccess();

        return rename($from, $to);
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array|bool
     */
    function url_stat($path, $flags)
    {
        $path = $this->cleanUpPath($path);

        if (!file_exists($path))
            return false;

        return stat($path);
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool|mixed
     * @throws \Exception
     */
    function mkdir($path, $mode, $options)
    {
        $path = $this->cleanUpPath($path);

        $this->assertWriteAccess();

        return mkdir($path, $mode, $options & STREAM_MKDIR_RECURSIVE);
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     * @throws \Exception
     */
    function rmdir($path, $options)
    {
        $path = $this->cleanUpPath($path);

        $this->assertWriteAccess();

        return rmdir($path);
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    function dir_opendir($path, $options)
    {
        $path = $this->cleanUpPath($path);

        $this->dh = opendir($path);

        return $this->dh > 0;
    }

    /**
     * @return string
     */
    function dir_readdir()
    {
        return readdir($this->dh);
    }

    /**
     * @return void
     */
    function dir_rewinddir()
    {
        rewinddir($this->dh);
    }

    /**
     * @return bool
     */
    function dir_closedir()
    {
        closedir($this->dh);

        $this->dh = false;

        return true;
    }
}
