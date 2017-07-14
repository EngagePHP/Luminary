<?php

namespace Luminary\Services\ApiQuery;

use Illuminate\Support\Arr;

class QueryArr extends Arr
{
    /**
     * Check if an array is a nested
     * associative array
     *
     * @param array $array
     * @return bool
     */
    public static function isNested(array $array)
    {
        return !is_null($array) && count(array_filter($array, 'is_array')) && static::isAssoc($array);
    }

    /**
     * Return an indexed array of dotted values to a multidimensional array
     * with the last dot being the value
     *
     * @param  array $array
     * @param string $defaultKey
     * @return array
     */
    public static function dotValue(array $array, string $defaultKey = 'default') :array
    {
        $results = [];

        foreach ($array as $item) {
            $item = explode('.', $item);
            $value = array_pop($item);
            $count = count($item);
            $last_key = array_pop($item) ?: $defaultKey;
            $result = [];

            switch (true) {
                case $count <= 1:
                    $result[$last_key] = [$value];
                    break;
                case $count > 1:
                    $last = [$last_key => $value];
                    foreach (array_reverse($item) as $key) {
                        $temp = [];
                        $temp[$key] = $last;
                        $result = $temp;
                        $last = $temp;
                    }
                    break;
            }

            $results = array_merge_recursive($results, $result);
        }

        return $results;
    }

    /**
     * Return a dotted array to a multi-dimensional associative array.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function dotReverse($array, $prepend = '') :array
    {
        $results = [];

        foreach ($array as $context => $value) {
            $context = explode('.', $context);
            $count = count($context);
            $last_key = array_pop($context);
            $result = [];

            switch (true) {
                case $count === 1:
                    $result[$last_key] = $value;
                    break;
                case $count > 1:
                    $last = [str_replace($prepend, '', $last_key) => $value];
                    foreach (array_reverse($context) as $key) {
                        $temp = [];
                        $temp[str_replace($prepend, '', $key)] = $last;
                        $result = $temp;
                        $last = $temp;
                    }
                    break;
            }

            $results = array_merge_recursive($results, $result);
        }

        return $results;
    }
}
