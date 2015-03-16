<?php
namespace Poirot\Stream\Interfaces;

interface iStreamable
{
    /**
     * Set Stream Handler Resource
     * 
     * @param iSResource $handle
     * 
     * @return $this
     */
    function setResource(iSResource $handle);

    /**
     * Get Stream Handler Resource
     * 
     * @return iSResource
     */
    function getResource();

    /**
     * @link http://php.net/manual/en/function.stream-copy-to-stream.php
     *
     * Copies Data From One Stream To Another
     *
     * @param iStreamable $destStream The destination stream
     * @param null    $inByte     Maximum bytes to copy
     * @param int     $offset     The offset where to start to copy data
     *
     * @return $this
     */
    function pipeTo(iStreamable $destStream, $inByte = null, $offset = 0);
    
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
     * @link http://php.net/manual/en/function.stream-socket-sendto.php
     *
     * Sends the specified data through the socket,
     * whether it is connected or not
     *
     * @param string $data  The data to be sent
     * @param int    $flags The value of flags can be any combination of the following:
     *                      STREAM_OOB	Process OOB (out-of-band) data.
     *
     * @return $this
     */
    function sendData($data, $flags = 0);

    /**
     * @link http://php.net/manual/en/function.fseek.php
     *
     * Move the file pointer to a new position
     *
     * - The new position, measured in bytes from the beginning of the file,
     *   is obtained by adding $offset to the position specified by $whence.
     *
     * ! php doesn't support seek/rewind on non-local streams
     *   we can using temp/cache piped stream.
     *
     * ! If you have opened the file in append ("a" or "a+") mode,
     *   any data you write to the file will always be appended,
     *   regardless of the file position.
     *
     * @param int $offset
     * @param int $whence Accepted values are:
     *              - SEEK_SET - Set position equal to $offset bytes.
     *              - SEEK_CUR - Set position to current location plus $offset.
     *              - SEEK_END - Set position to end-of-file plus $offset.
     */
    function seek($offset, $whence = SEEK_SET);

    /**
     * @link http://php.net/manual/en/function.rewind.php
     *
     * Move the file pointer to the beginning of the stream
     *
     * ! php doesn't support seek/rewind on non-local streams
     *   we can using temp/cache piped stream.
     *
     * ! If you have opened the file in append ("a" or "a+") mode,
     *   any data you write to the file will always be appended,
     *   regardless of the file position.
     *
     * @return $this
     */
    function rewind();

    /**
     * @link  http://php.net/manual/en/function.stream-socket-shutdown.php
     *
     * Close Stream Resource
     *
     * @return null
     */
    function close();
}
