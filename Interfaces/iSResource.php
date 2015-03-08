<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\Resource\iSResMetaReader;

interface iSResource
{
    /**
     * Get Resource Origin Handler
     *
     * @return resource
     */
    function getResource();

    /**
     * @link http://php.net/manual/en/function.stream-socket-get-name.php
     *
     * Retrieve the name of the local sockets
     *
     * @return string
     */
    function getLocalName();

    /**
     * @link http://php.net/manual/en/function.stream-socket-get-name.php
     *
     * Retrieve the name of the remote sockets
     *
     * @return string
     */
    function getRemoteName();

    /**
     * Meta Data About Handler
     *
     * @return iSResMetaReader
     */
    function meta();

    // :

    /**
     * @link http://php.net/manual/en/function.ftell.php
     *
     * Get the position of the file pointer
     *
     * @return int
     */
    function getCurrOffset();

    /**
     * @link http://php.net/manual/en/function.feof.php
     *
     * Is Stream Positioned At The End?
     *
     * @return boolean
     */
    function isEOF();

    /**
     * @link http://php.net/manual/en/function.stream-is-local.php
     *
     * Checks If Stream Is Local One Or Not?
     *
     * @return boolean
     */
    function isLocal();

    /**
     * Is Stream Alive?
     *
     * - is_readable uri meta data?
     *
     * @return boolean
     */
    function isAlive();

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
}
