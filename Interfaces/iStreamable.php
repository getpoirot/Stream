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
     * Copies Data From One Stream To Another
     *
     * - If maxlength is not specified,
     *   all remaining content in source will be copied
     *
     * - reset and count into transCount
     *
     * @param iStreamable $destStream The destination stream
     * @param null        $maxByte    Maximum bytes to copy
     * @param int         $offset     The offset where to start to copy data
     *
     * @return $this
     */
    function pipeTo(iStreamable $destStream, $maxByte = null, $offset = 0);
    
    /**
     * Read Data From Stream
     *
     * - if $inByte argument not set, read entire stream
     *
     * @param int  $inByte Read Data in byte
     *
     * @throws \Exception Error On Read Data
     * @return string
     */
    function read($inByte = null);

    /**
     * Gets line from stream resource up to a given delimiter
     *
     * Reading ends when length bytes have been read,
     * when the string specified by ending is found
     * (which is not included in the return value),
     * or on EOF (whichever comes first)
     *
     * ! does not return the ending delimiter itself
     *
     * @param string $ending
     * @param int    $inByte
     *
     * @return string
     */
    function readLine($ending = "\n", $inByte = null);

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
     * Sends the specified data through the socket,
     * whether it is connected or not
     *
     * @param string $data  The data to be sent
     * @param int    $flags The value of flags can be any combination of the following:
     *                      STREAM_OOB	Process OOB (out-of-band) data.
     *
     * @return $this
     */
    function sendData($data, $flags = STREAM_OOB);

    /**
     * Receives data from a socket, connected or not
     *
     * @param int $maxByte
     * @param int $flags
     *
     * @return string
     */
    function receiveFrom($maxByte, $flags = STREAM_OOB);

    /**
     * Get Total Count Of Bytes After Each Read/Write
     *
     * @return int
     */
    function getTransCount();

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
     *
     * @return $this
     */
    function seek($offset, $whence = SEEK_SET);

    /**
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
}
