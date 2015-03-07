<?php
namespace Poirot\Stream\Interfaces;

interface iStream 
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
    const MODE_WBCT  = 'W' ;
    const MODE_RWBCT = 'W+';
    const MODE_WAC   = 'a' ;
    const MODE_RWAC  = 'a+';
    const MODE_WBX   = 'X' ;
    const MODE_RWBX  = 'X+';
    const MODE_WBC   = 'C' ;
    const MODE_RWBC  = 'C+';

    /**
     * Construct
     * 
     * @param iStreamResource $handle
     */
    function __construct(iStreamResource $handle);

    /**
     * Set Stream Handler Resource
     * 
     * @param iStreamResource $handle
     * 
     * @return $this
     */
    function setResource(iStreamResource $handle);

    /**
     * Get Stream Handler Resource
     * 
     * @return iStreamResource
     */
    function getResource();

    /**
     * @link http://php.net/manual/en/function.stream-copy-to-stream.php
     *
     * Copies Data From One Stream To Another
     *
     * @param iStream $destStream The destination stream
     * @param null    $inByte     Maximum bytes to copy
     * @param int     $offset     The offset where to start to copy data
     *
     * @return $this
     */
    function pipeTo(iStream $destStream, $inByte = null, $offset = 0);
    
    /**
     * @link http://php.net/manual/en/function.stream-get-contents.php
     *
     * Read Data From Stream
     *
     * - if $inByte argument not set, read entire stream
     *
     * @param int  $inByte Read Data in byte
     *
     * @return string
     */
    function read($inByte = null);

    /**
     * @link http://php.net/manual/en/function.stream-get-line.php
     *
     * Reading ends when length bytes have been read,
     * when the string specified by ending is found
     * (which is not included in the return value),
     * or on EOF (whichever comes first)
     *
     * ! does not return the ending delimiter itself
     *
     * @param int    $inByte
     * @param string $ending
     *
     * @return string
     */
    function readLine($inByte = null, $ending = "\n");

    /**
     * Writes the contents of string to the file stream
     *
     * @param string $content   The string that is to be written
     * @param int    $inByte    Writing will stop after length bytes
     *                          have been written or the end of string
     *                          is reached
     * 
     * @return $this
     */
    function write($content, $inByte = null);

    /**
     * Move the file pointer to a new position
     *
     * The new position, measured in bytes from the beginning of the file,
     * is obtained by adding $offset to the position specified by $whence.
     *
     * @param int $offset
     * @param int $whence Accepted values are:
     *              - SEEK_SET - Set position equal to $offset bytes.
     *              - SEEK_CUR - Set position to current location plus $offset.
     *              - SEEK_END - Set position to end-of-file plus $offset.
     */
    function seek($offset, $whence = SEEK_SET);

    /**
     * Move the file pointer to the beginning of the stream
     *
     * @return $this
     */
    function rewind();

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Readable?
     *
     * @return boolean
     */
    function isReadable();

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Writable?
     *
     * @return boolean
     */
    function isWritable();

    /**
     * @see iSHMeta
     *
     * Check Whether Stream Resource Is Seekable?
     *
     * @return boolean
     */
    function isSeekable();

    /**
     * @link  http://php.net/manual/en/function.stream-socket-shutdown.php
     *
     * Close Stream Resource
     *
     * @return null
     */
    function close();
}
