<?php
namespace Poirot\Stream\Wrapper;

use Psr\Http\Message\StreamInterface;

use Poirot\Stream\Context\ContextStreamBase;
use Poirot\Stream\Resource\AccessMode;
use Poirot\Stream\Wrapper\PsrToPhpResource\OptsPsrToPhpResource;

/**
 * Wrapper to Convert StreamInterface into
 * PHP resource stream
 *
 * if ($rHandler instanceof StreamInterface)
 *    $rHandler = SPsrWrapper::convertToResource($rHandler);
 */
class WrapperPsrToPhpResource 
    extends aWrapperStream
{
    /** @var string Open Mode */
    protected $_w__mode;
    /** @var StreamInterface */
    protected $_w__stream;

    /**
     * Get Wrapper Protocol Label
     *
     * - used on register/unregister wrappers, ...
     *
     *   label://
     *   -----
     *
     * @return string
     */
    function getLabel()
    {
        return 'psr';
    }

    /**
     * Convert StreamInterface To PHP resource
     *
     * @param StreamInterface $stream
     *
     * @return resource
     */
    static function convertToResource(StreamInterface $stream)
    {
        # register wrapper if not
        $self = new self;
        if(!RegistryOfWrapperStream::isRegistered($self->getLabel()))
            RegistryOfWrapperStream::register($self);


        # make resource
        $mode = new AccessMode();
        (!$stream->isWritable()) ?: $mode->openForWrite();
        (!$stream->isReadable()) ?: $mode->openForRead();

        $label = $self->getLabel();
        return fopen($label.'://stream'
            , (string) $mode
            , null
            ## set options to wrapper
            , (new ContextStreamBase($label, array('stream' => $stream)))->toContext()
        );
    }


    // Implement Wrapper:

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $stream = $this->optsData()->getStream();
        if (!$stream || !$stream instanceof StreamInterface)
            return false;

        $this->_w__mode   = $mode;
        $this->_w__stream = $stream;

        return true;
    }

    public function stream_read($count)
    {
        return $this->_w__stream->read($count);
    }

    public function stream_write($data)
    {
        return (int) $this->_w__stream->write($data);
    }

    public function stream_tell()
    {
        return $this->_w__stream->tell();
    }

    public function stream_eof()
    {
        return $this->_w__stream->eof();
    }

    public function stream_seek($offset, $whence)
    {
        $this->_w__stream->seek($offset, $whence);

        return true;
    }

    public function stream_stat()
    {
        static $modeMap = array(
            'r'  => 33060,
            'r+' => 33206,
            'w'  => 33188
        );

        return array(
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => $modeMap[$this->_w__mode],
            'nlink'   => 0,
            'uid'     => 0,
            'gid'     => 0,
            'rdev'    => 0,
            'size'    => $this->_w__stream->getSize() ?: 0,
            'atime'   => 0,
            'mtime'   => 0,
            'ctime'   => 0,
            'blksize' => 0,
            'blocks'  => 0
        );
    }


    // ...

    /**
     * @override ide completion
     * @return OptsPsrToPhpResource
     */
    function optsData()
    {
        return parent::optsData();
    }

    /**
     * @override ide completion
     * @param null|mixed $builder Builder Options as Constructor
     * @return OptsPsrToPhpResource
     */
    static function newOptsData($builder = null)
    {
        return new OptsPsrToPhpResource($builder);
    }
}
