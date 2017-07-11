<?php

namespace Luminary\Services\ApiQuery;

class QueryParser
{
    /**
     * Run Parser methods on array
     *
     * @param array $query
     * @return array
     */
    public static function parse(array $query) :array
    {
        $sanitized = static::sanitize($query);
        return static::expand($sanitized);
    }

    /**
     * Expand comma separated array values to arrays
     *
     * @param array $array
     * @return array
     */
    public static function expand(array $array) :array
    {
        return collect($array)->map(
            function ($item) {
                if (is_array($item)) {
                    return static::expand($item);
                }

                $split = static::splitList($item);
                return count($split) > 1 ? $split : $item;
            }
        )->toArray();
    }

    /**
     * Clean array keys of unsanitized values
     *
     * @param array $array
     * @return array
     */
    public static function sanitize(array $array) :array
    {
        return collect($array)->mapWithKeys(
            function ($value, $key) {
                $key = is_int($key) ? $key: static::replaceWithUnderscores(static::stripQuotes($key));
                $value = is_array($value) ? static::sanitize($value) : $value;

                return [$key => $value];
            }
        )->toArray();
    }

    /**
     * Strip extra quotes from a string
     *
     * @param string $str
     * @return string
     */
    public static function stripQuotes(string $str) :string
    {
        return preg_replace('/(^[\"\']|[\"\']$)/', '', $str);
    }

    /**
     * Replace slashes, dashes, and spaces to underscores
     *
     * @param $str
     * @return string
     */
    public static function replaceWithUnderscores(string $str) :string
    {
        return str_replace(['/', '-', ' '], '_', $str);
    }

    /**
     * Convert comma separated values to arrays
     *
     * @param string $list
     * @return array
     */
    public static function splitList(string $list) :array
    {
        return is_null($list) ? [] : preg_split('/ ?, ?/', $list);
    }
}
