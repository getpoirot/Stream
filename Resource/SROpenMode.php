<?php
namespace Poirot\Stream\Resource;

use Poirot\Stream\Interfaces\Resource\iSRAccessMode;

class SROpenMode implements iSRAccessMode
{
    /**
     * Construct
     *
     * - use toString method
     *
     * @param null|string $modeStr
     */
    function __construct($modeStr = null)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * Set From String
     *
     * @param string $modStr
     *
     * @return $this
     */
    function fromString($modStr)
    {
        // TODO: Implement fromString() method.
    }

    /**
     * Open File For Write
     *
     * @return $this
     */
    function openForWrite()
    {
        // TODO: Implement openForWrite() method.
    }

    /**
     * Open File For Read
     *
     * @return $this
     */
    function openForRead()
    {
        // TODO: Implement openForRead() method.
    }

    /**
     * Indicates whether the mode allows to read
     *
     * @return boolean
     */
    function hasAllowRead()
    {
        // TODO: Implement hasAllowRead() method.
    }

    /**
     * Indicates whether the mode allows to write
     *
     * @return boolean
     */
    function hasAllowWrite()
    {
        // TODO: Implement hasAllowWrite() method.
    }

    /**
     * Open Stream as Binary Mode
     *
     * @return $this
     */
    function asBinary()
    {
        // TODO: Implement asBinary() method.
    }

    /**
     * Open Stream as Plain Text
     *
     * @see http://php.net/manual/en/function.fopen.php
     *      first note
     *
     * @return $this
     */
    function asText()
    {
        // TODO: Implement asText() method.
    }

    /**
     * Indicates whether the stream is in binary mode
     *
     * @return boolean
     */
    function isBinary()
    {
        // TODO: Implement isBinary() method.
    }

    /**
     * Indicates whether the stream is in text mode
     *
     * @return boolean
     */
    function isText()
    {
        // TODO: Implement isText() method.
    }

    /**
     * Place the file pointer at the end of the file
     *
     * @return $this
     */
    function withPointerAtEnd()
    {
        // TODO: Implement withPointerAtEnd() method.
    }

    /**
     * Place the file pointer at the beginning of the file
     *
     * @return $this
     */
    function withPointerAtBeginning()
    {
        // TODO: Implement withPointerAtBeginning() method.
    }

    /**
     * Indicates whether the mode implies positioning the cursor at the
     * beginning of the file
     *
     * @return boolean
     */
    function isAtTop()
    {
        // TODO: Implement isAtTop() method.
    }

    /**
     * Indicates whether the mode implies positioning the cursor at the end of
     * the file
     *
     * @return boolean
     */
    function isAtEnd()
    {
        // TODO: Implement isAtEnd() method.
    }

    /**
     * Create File If the file does not exist
     *
     * @return $this
     */
    function createFile()
    {
        // TODO: Implement createFile() method.
    }

    /**
     * Create file only if not exists
     *
     * - not create if file exists
     *
     * ! otherwise fail
     *
     * @return $this
     */
    function createXFile()
    {
        // TODO: Implement createXFile() method.
    }

    /**
     * Indicates whether the mode allows to create a new file
     *
     * @return boolean
     */
    function hasCreate()
    {
        // TODO: Implement hasCreate() method.
    }

    /**
     * Indicates whether the mode allows to open an existing file
     *
     * @return boolean
     */
    function hasXCreate()
    {
        // TODO: Implement hasXCreate() method.
    }

    /**
     * Truncate file after open
     *
     * @return $this
     */
    function doTruncate()
    {
        // TODO: Implement doTruncate() method.
    }

    /**
     * Indicates whether the mode implies to delete the
     * existing content of the file
     *
     * @return boolean
     */
    function hasTruncate()
    {
        // TODO: Implement hasTruncate() method.
    }

    /**
     * Get Access Mode As String
     *
     * @return string
     */
    function toString()
    {
        // TODO: Implement toString() method.
    }

    /**
     * Magical String Object
     *
     * @return string
     */
    function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
 