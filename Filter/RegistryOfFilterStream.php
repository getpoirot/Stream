<?php
namespace Poirot\Stream\Filter;

use Poirot\Stream\Interfaces\Filter\iFilterStream;
use Poirot\Stream\Interfaces\Filter\iRegistryOfFilterStream;

class RegistryOfFilterStream 
    implements iRegistryOfFilterStream
{
    protected static $filters = array();

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
    static function register(iFilterStream $filter, $label = null)
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
     * @return iFilterStream
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
     * @param string|iFilterStream $filterName
     *
     * @return boolean
     */
    static function has($filterName)
    {
        if ($filterName instanceof iFilterStream)
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
