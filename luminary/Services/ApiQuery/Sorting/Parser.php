<?php

namespace Luminary\Services\ApiQuery\Sorting;

use Luminary\Services\ApiQuery\QueryArr;
use Luminary\Services\ApiQuery\QueryParser;

class Parser extends QueryParser
{
    /**
     * Run Parser methods on array
     *
     * @param array $sorting
     * @param string $defaultKey
     * @return array
     */
    public static function parse(array $sorting, $defaultKey = 'default') :array
    {
        return collect($sorting)->keyBy(
            function ($value) use ($defaultKey) {
                $key = trim($value, '-');
                return strpos($key, '.') === false ? implode('.', [$defaultKey, $key]) : $key;
            }
        )->map(
            function ($value) {
                return mb_substr($value, 0, 1, 'utf-8') != '-' ? 'ASC' : 'DESC';
            }
        )
        ->pipe(
            function ($sorting) {
                return QueryArr::dotReverse($sorting);
            }
        );
    }
}
