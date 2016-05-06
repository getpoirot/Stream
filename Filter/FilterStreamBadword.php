<?php
namespace Poirot\Stream\Filter;

/**
 * An Example To How To Write New Filter
 */
class FilterStreamBadword 
    extends aFilterStreamCustom
{
    /**
     * Filter Stream Through Buckets
     *
     * @param resource $in     userfilter.bucket brigade
     *                         pointer to a group of buckets objects containing the data to be filtered
     * @param resource $out    userfilter.bucket brigade
     *                         pointer to another group of buckets for storing the converted data
     * @param int $consumed    counter passed by reference that must be incremented by the length
     *                         of converted data
     * @param boolean $closing flag that is set to TRUE if we are in the last cycle and the stream is
     *                           about to close
     * @return int
     */
    function filter($in, $out, &$consumed, $closing)
    {
        $unfiltered = $this->_getDataFromBucket($in, $consumed);

        // Filter Stream Data:
        $filtered = $unfiltered;
        foreach ($this->params['bad_words'] as $badWord) {
            $regex = "[(^|\s)({$badWord})($|\s)]i";
            preg_match_all($regex, $filtered, $matches);
            $filtered = preg_replace($regex, "$1{$this->_getReplacement()}$3", $filtered);
        }

        // Write Down Back Filtered Data To Stream:
        return $this->_writeBackDataOut($out, $filtered);
    }

    /**
     * @return string
     */
    protected function _getReplacement()
    {
        return '@!#^#!@';
    }
}
