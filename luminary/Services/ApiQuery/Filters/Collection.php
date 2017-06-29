<?php

namespace Luminary\Services\ApiQuery\Filters;

use Illuminate\Support\Collection as BaseCollection;
use Luminary\Services\ApiQuery\Filters\Parser as FilterParser;

class Collection extends BaseCollection
{
    /**
     * Create a new collection.
     *
     * @param array $items
     * @param string $resource
     */
    public function __construct(array $items = [], string $resource = 'default')
    {
        parent::__construct($items);

        $this->items = $this->parseDefaults($resource);
    }

    /**
     * Setup the filter collection with
     * the correct default resource array
     *
     * @param string $resource
     * @return array
     */
    protected function parseDefaults(string $resource)
    {
        $filters = $this->all();
        $parser = new FilterParser;

        // Filter numeric and default resource
        $filtered = $parser->filterIndexed($filters);

        // Pull the default resource and merge with filtered
        $defaults = array_merge_recursive(
            array_pull($filters, $resource, []),
            array_only($filters, $parser->getQueryTypes()),
            $filtered
        );

        // Create the new filters array
        $associated = $parser->associateIndexedQueries($defaults);
        $except = array_merge(array_keys($filtered), $parser->getQueryTypes());

        // Create the updated filters array
        return collect($filters)->except($except)->put($resource, $associated)->filter()->toArray();
    }
}
