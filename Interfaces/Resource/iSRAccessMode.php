<?php
namespace Poirot\Stream\Interfaces\Resource;

interface iSRAccessMode
{
    /*++
    Stream File Open, Words Stand For:

    R = Read                 | W = Write
    -----------------------------------------------------------------------------
    A = Pointer at end       | B = Pointer at beginning
    -----------------------------------------------------------------------------
    C = Create if not exists | X = Create file only if not exists, otherwise fail
    -----------------------------------------------------------------------------
    T = Truncate file

    @see http://php.net/manual/en/function.fopen.php
    ++*/
    const MODE_RB    = 'r' ;
    const MODE_RWB   = 'r+';
    const MODE_WBCT  = 'w' ;
    const MODE_RWBCT = 'w+';
    const MODE_WAC   = 'a' ;
    const MODE_RWAC  = 'a+';
    const MODE_WBX   = 'x' ;
    const MODE_RWBX  = 'x+';
    const MODE_WBC   = 'c' ;
    const MODE_RWBC  = 'c+';

    /**
     * Construct
     *
     * - use toString method
     *
     * @param null|string $modeStr
     */
    function __construct($modeStr = null);

    /**
     * Set From String
     *
     * - reset object
     *
     * @param string $modStr
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function fromString($modStr);

    // Access Modes Implementations:

    /**
     * Open File For Write
     *
     * @return $this
     */
    function openForWrite();

    //   +   //

    /**
     * Open File For Read
     *
     * @return $this
     */
    function openForRead();

    //

    /**
     * Indicates whether the mode allows to read
     *
     * @return boolean
     */
    function hasAllowRead();

    /**
     * Indicates whether the mode allows to write
     *
     * @return boolean
     */
    function hasAllowWrite();

    // _____

    /**
     * Open Stream as Binary Mode
     *
     * @return $this
     */
    function asBinary();

    /**
     * Open Stream as Plain Text
     *
     * @see http://php.net/manual/en/function.fopen.php
     *      look at first note
     *
     * @return $this
     */
    function asText();

    //

    /**
     * Indicates whether the stream is in binary mode
     *
     * @return boolean
     */
    function isBinary();

    /**
     * Indicates whether the stream is in text mode
     *
     * @return boolean
     */
    function isText();

    // _____

    /**
     * Place the file pointer at the end of the file
     *
     * @return $this
     */
    function withPointerAtEnd();

    //   or  //

    /**
     * Place the file pointer at the beginning of the file
     *
     * @return $this
     */
    function withPointerAtBeginning();

    //

    /**
     * Indicates whether the mode implies positioning the cursor at the
     * beginning of the file
     *
     * @return boolean
     */
    function isAtTop();

    /**
     * Indicates whether the mode implies positioning the cursor at the end of
     * the file
     *
     * @return boolean
     */
    function isAtEnd();

    // _____

    /**
     * Create File If the file does not exist
     *
     * @return $this
     */
    function createFile();

    /**
     * Create file only if not exists
     *
     * - not create if file exists
     *
     * ! otherwise fail
     *
     * @return $this
     */
    function createXFile();

    //

    /**
     * Indicates whether the mode allows to create a new file
     *
     * @return boolean
     */
    function hasCreate();

    /**
     * Indicates whether the mode allows to open an existing file
     *
     * @return boolean
     */
    function hasXCreate();

    // _____

    /**
     * Truncate file after open
     *
     * @return $this
     */
    function doTruncate();

    //

    /**
     * Indicates whether the mode implies to delete the
     * existing content of the file
     *
     * @return boolean
     */
    function hasTruncate();

    // :

    /**
     * Get Access Mode As String
     *
     * - usually in format of W, r+, rb+, ...
     *
     * @throws \Exception If not complete statement
     * @return string
     */
    function toString();
}
