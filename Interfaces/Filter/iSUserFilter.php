<?php
namespace Poirot\Stream\Interfaces\Filter;

/**
 * stream_filter_register() must be called first in order
 * to register the desired user filter to filtername.
 *
 * Using iSFManager To Register Filters
 *
 * Filters Manipulate Every Chunk Of Data That Read/Write
 * Separately on each action
 *
 */
interface iSUserFilter extends ipSFilter
{
    /*
    php_user_filter prototype
    */

    /**
     * Filter data.
     * This method is called whenever data is read from or written to the attach
     * stream.
     *
     * @param   resource  $in           A resource pointing to a bucket brigade
     *                                  which contains one or more bucket
     *                                  objects containing data to be filtered.
     * @param   resource  $out          A resource pointing to a second bucket
     *                                  brigade into which your modified buckets
     *                                  should be replaced.
     * @param   int       &$consumed    Which must always be declared by
     *                                  reference, should be incremented by the
     *                                  length of the data which your filter
     *                                  reads in and alters.
     * @param   bool      $closing      If the stream is in the process of
     *                                  closing (and therefore this is the last
     *                                  pass through the filterchain), the
     *                                  closing parameter will be set to true.
     * @return  int
     */
     function filter($in, $out, &$consumed, $closing);

    /**
     * Called respectively when our class is created
     *
     * @return  bool
     */
    function onCreate();

    /**
     * Called upon filter shutdown (typically, this is also during stream
     * shutdown), and is executed after the flush method is called.
     *
     * @return  void
     */
    function onClose();
}
