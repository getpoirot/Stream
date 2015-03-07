<?php
namespace Poirot\Stream\Interfaces;

use Poirot\Stream\Interfaces\StreamResource\iSRMeta;

interface iStreamResource
{
    /**
     * Construct
     *
     * @param resource $stream
     *
     * @throws \InvalidArgumentException
     */
    function __construct($stream);

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
     * @return iSRMeta
     */
    function meta();

    // :

    /**
     * Get the position of the file pointer
     *
     * @return int
     */
    function getCurrOffset();

    /**
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
     * @return boolean
     */
    function isAlive();
}
