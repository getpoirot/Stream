<?php
namespace Poirot\Stream\Interfaces\Filter;

interface iSFManager 
{
    /**
     * Register a user defined stream filter
     *
     * - when the filter registered it can't be removed
     *
     * @param ipSFilter  $filter
     * @param null      $label   Wrapper Label
     *                           - If Not Set Using iSFilter
     *
     * @throws \Exception If Wrapper Registered Before
     *                    Error on Registering Filter
     */
    static function register(ipSFilter $filter, $label = null);

    /**
     * Get Filter By Name
     *
     * @param string $filterName
     *
     * @throws \Exception Filter Not Found
     * @return ipSFilter
     */
    static function get($filterName);

    /**
     * Has Filter ?
     *
     * @param string|ipSFilter $filterName
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
