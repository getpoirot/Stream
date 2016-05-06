<?php
namespace Poirot\Stream\Interfaces\Filter;

interface iRegistryOfFilterStream 
{
    /**
     * Register a user defined stream filter
     *
     * - when the filter registered it can't be removed
     *
     * @param iFilterStream  $filter
     * @param null           $label   Wrapper Label
     *                                - If Not Set Using iFilterStream
     *
     * @throws \Exception If Wrapper Registered Before
     *                    Error on Registering Filter
     */
    static function register(iFilterStream $filter, $label = null);

    /**
     * Get Filter By Name
     *
     * @param string $filterName
     *
     * @throws \Exception Filter Not Found
     * @return iFilterStream
     */
    static function get($filterName);

    /**
     * Has Filter ?
     *
     * @param string|iFilterStream $filterName
     *
     * @return boolean
     */
    static function has($filterName);

    /**
     * Get List Of Registered Filters
     *
     * @return array [string]
     */
    static function listFilters();
}
