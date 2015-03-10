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
     * @param string $modStr
     *
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

    // _____

    /**
     * Open Stream as Binary Mode
     *
     * @return $this
     */
    function asBinary();

    /**
     * Open Stream as Plain String
     *
     * @return $this
     */
    function asString();

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
     * ! otherwise fail
     *
     * @return $this
     */
    function createXFile();

    // _____

    /**
     * Truncate file after open
     *
     * @return $this
     */
    function doTruncate();

    // :

    /**
     * Get Access Mode As String
     *
     * @return string
     */
    function toString();

    /**
     * Magical String Object
     *
     * @return string
     */
    function __toString();
}
