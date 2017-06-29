<?php

namespace Luminary\Services\ApiQuery\Pagination;

use Luminary\Services\ApiQuery\QueryArr;
use Luminary\Services\ApiQuery\QueryParser;

class Parser extends QueryParser
{
    /**
     * Parse paginate params
     *
     * @param array $paginate
     * @return array
     */
    public static function parse(array $paginate) :array
    {
        if (empty($paginate)) {
            return [];
        }

        return [
            'page' => array_get($paginate, 'number', 1),
            'per_page' => array_get($paginate, 'size', 10)
        ];
    }
}
