<?php
namespace Poirot\Stream\Interfaces\Filter;

interface iSFManager 
{
    /**
     * Register a user defined stream filter
     *
     * @param iSFilter  $filter
     * @param null      $label   Wrapper Label
     *                           - If Not Set Using iSFilter
     *
     * @throws \Exception If Wrapper Registered Before
     *                    Error on Registering Filter
     */
    static function register(iSFilter $filter, $label = null);

    /**
     * Remove Filter
     *
     * @param string|iSFilter $filter
     *
     * @param $filter
     */
    static function unregister($filter);

    /**
     * Get Filter By Name
     *
     * @param string $filterName
     *
     * @throws \Exception Filter Not Found
     * @return iSFilter
     */
    static function get($filterName);

    /**
     * Has Filter ?
     *
     * @param string|iSFilter $filterName
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
