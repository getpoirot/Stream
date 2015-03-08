<?php
namespace Poirot\Stream\Interfaces\Filter;

interface iSFManager 
{
    /**
     * Register a user defined stream filter
     *
     * @param iSFilter  $wrapper
     * @param null      $label   Wrapper Label
     *                           - If Not Set Using iSFilter
     *
     * @throw \Exception If Wrapper Registered Before
     */
    static function register(iSFilter $wrapper, $label = null);

    /**
     * @link http://php.net/manual/en/function.stream-filter-remove.php
     *
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
     * @link http://php.net/manual/en/function.stream-get-filters.php
     *
     * Get List Of Registered Filters
     *
     * @return [string]
     */
    static function listFilters();
}
