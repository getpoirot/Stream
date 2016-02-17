<?php
namespace Poirot\Stream;

use Poirot\Stream\Interfaces\Filter\ipSFilter;
use Poirot\Stream\Interfaces\Filter\iSFManager;

class SFilterManager implements iSFManager
{
    protected static $filters = [];

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
    static function register(ipSFilter $filter, $label = null)
    {
        $name = $filter->getLabel();

        if (self::has($name))
            throw new \Exception(sprintf(
                'Filter "%s" is registered, and cant be overwritten.',
                $name
            ));

        if (stream_filter_register($name, get_class($filter)) === false)
            throw new \Exception(sprintf(
                'Error On Registering "%s" Filter.',
                $name
            ));

        self::$filters[$name] = $filter;
    }

    /**
     * Get Filter By Name
     *
     * @param string $filterName
     *
     * @throws \Exception Filter Not Found
     * @return ipSFilter
     */
    static function get($filterName)
    {
        if (!self::has($filterName))
            throw new \Exception(sprintf(
                'Filter "%s" Not Found.',
                $filterName
            ));

        return self::$filters[$filterName];
    }

    /**
     * Has Filter ?
     *
     * @param string|ipSFilter $filterName
     *
     * @return boolean
     */
    static function has($filterName)
    {
        if ($filterName instanceof ipSFilter)
            $filterName = $filterName->getLabel();

        $result = in_array($filterName, self::listFilters());

        return $result;
    }

    /**
     * Get List Of Registered Filters
     *
     * @return array [string]
     */
    static function listFilters()
    {
        return stream_get_filters();
    }
}
 