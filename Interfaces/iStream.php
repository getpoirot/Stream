<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

use Poirot\Filesystem\Interfaces\Filesystem\Stream\iStreamMeta;

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
    const STREAM_RB    = 'r';
    const STREAM_RWB   = 'r+';
    const STREAM_WBCT  = 'W';
    const STREAM_RWBCT = 'W+';
    const STREAM_WAC   = 'a';
    const STREAM_RWAC  = 'a+';
    const STREAM_WBX   = 'X';
    const STREAM_RWBX  = 'X+';
    const STREAM_WBC   = 'C';
    const STREAM_RWBC  = 'C+';

    /**
     * Set Resource Handler
     *
     * - Usually Handler injected from filesystem::stream()
     *
     * @param resource $resource Resource Handler
     *
     * @return $this
     */
    function setHandler($resource);

    /**
     * Meta Data About Handler
     *
     * @return iStreamMeta
     */
    function meta();

    /**
     * Read Data From Stream
     *
     * @param int  $byte       Read Data in byte
     * @param bool $binarySafe Binary Safe Data Read
     *
     * @return string
     */
    function read($byte = 0, $binarySafe = false);

    /**
     * Writes the contents of string to the file stream
     *
     * @param string $content The string that is to be written
     * @param int    $byte    writing will stop after length bytes
     *                        have been written or the end of string
     *                        is reached
     *
     * @return $this
     */
    function write($content, $byte = 0);

    /**
     * Is At The End Of Stream?
     *
     * !  If PHP is not properly recognizing the line endings
     *    when reading files either on or created by a Macintosh computer,
     *    enabling the auto_detect_line_endings run-time configuration
     *    option may help resolve the problem
     *
     * @return bool
     */
    function isStreamEnd();

    /**
     * Close Stream Resource
     *
     * @return null
     */
    function close();
}
