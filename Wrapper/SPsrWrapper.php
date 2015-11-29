<?php
namespace Poirot\Stream\Wrapper;

use Poirot\Stream\Context\BaseContext;
use Poirot\Stream\Psr\StreamInterface;
use Poirot\Stream\Resource\SROpenMode;
use Poirot\Stream\SWrapperManager;
use Poirot\Stream\Wrapper\Psr\SPsrOpts;

/**
 * Wrapper to Convert StreamInterface into
 * PHP resource stream
 *
 * if ($rHandler instanceof StreamInterface)
 *    $rHandler = SPsrWrapper::convertToResource($rHandler);
 */
class SPsrWrapper extends AbstractWrapper
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
        if(!SWrapperManager::isRegistered($self->getLabel()))
            SWrapperManager::register($self);


        # make resource
        $mode = new SROpenMode;
        (!$stream->isWritable()) ?: $mode->openForWrite();
        (!$stream->isReadable()) ?: $mode->openForRead();

        $label = $self->getLabel();
        return fopen($label.'://stream'
            , (string) $mode
            , null
            ## set options to wrapper
            , (new BaseContext($label, ['stream' => $stream]))->toContext()
        );
    }


    // Implement Wrapper:

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $stream = $this->options()->getStream();
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
        static $modeMap = [
            'r'  => 33060,
            'r+' => 33206,
            'w'  => 33188
        ];

        return [
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
        ];
    }


    // ...

    /**
     * @override ide completion
     * @return SPsrOpts
     */
    function options()
    {
        return parent::options();
    }

    /**
     * @override ide completion
     * @return SPsrOpts
     */
    static function optionsIns()
    {
        return new SPsrOpts;
    }
}
