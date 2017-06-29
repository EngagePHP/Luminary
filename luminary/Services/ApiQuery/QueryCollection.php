<?php

namespace Luminary\Services\ApiQuery;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Luminary\Services\ApiQuery\Filters\Parser as FilterParser;
use Luminary\Services\ApiQuery\Filters\Collection as FilterCollection;
use Luminary\Services\ApiQuery\Pagination\Parser as PaginationParser;
use Luminary\Services\ApiQuery\Sorting\Parser as SortingParser;

class QueryCollection extends Collection
{
    /**
     * Available query keys
     *
     * @var array
     */
    protected $keys = [
        'fields',
        'filter',
        'include',
        'paginate',
        'resource',
        'sort'
    ];

    /**
     * Create a new collection.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);

        $this->items = Arr::only($this->items, $this->keys);

        $this->parse();
    }

    /**
     * Filter and Run the query parser on the
     * collection of items
     *
     * @return QueryCollection
     */
    private function parse() :QueryCollection
    {
        $this->items = collect($this->all())->filter()->pipe(function (Collection $self) {
            return QueryParser::parse($self->all());
        });

        return $this;
    }

    /**
     * Return all the fields or
     * fields by resource name
     *
     * @param string|null $resource
     * @return array
     */
    public function fields(string $resource = null) :array
    {
        $fields = $this->get('fields', ['*']);

        return is_null($resource) ? $fields : (array) collect($fields)->get($resource, ['*']);
    }

    /**
     * Return all of the filters or
     * filters by resource name
     *
     * @param string|null $resource
     * @return array
     */
    public function filters(string $resource = null) :array
    {
        $filters = new FilterCollection($this->get('filter', []), $this->resource());

        if (is_null($resource)) {
            return $filters->map(
                function ($resource) {
                    return FilterParser::parse($resource);
                }
            )->toArray();
        }

        return FilterParser::parse($filters->get($resource, []));
    }

    /**
     * Return the includes array
     *
     * @return array
     */
    public function include() :array
    {
        return (array) $this->get('include', []);
    }

    /**
     * Return the query pagination
     *
     * @return array
     */
    public function pagination() :array
    {
        return PaginationParser::parse(
            $this->get('paginate', [])
        );
    }

    /**
     * Return the query default resource
     *
     * @return string
     */
    public function resource() :string
    {
        return (string) $this->get('resource', 'default');
    }

    /**
     * Return the sorting array
     *
     * @return array
     */
    public function sorting(string $resource = null) :array
    {
        $sorting = SortingParser::parse(
            $this->get('sort', []),
            $this->resource()
        );

        if (is_null($resource)) {
            return $sorting;
        }

        return collect(array_get($sorting, $resource))->filter(
            function ($value) {
                return ! is_array($value);
            }
        )->all();
    }

    /**
     * Get values by entity
     *
     * @param Closure $closure
     * @param Closure $return
     * @return QueryCollection
     */
    public function filterNested(Closure $closure, Closure $return = null) :QueryCollection
    {
        return $this->map(
            function ($value, $key) use ($closure, $return) {
                if (QueryArr::isNested($value)) {
                    return $closure($value, $key);
                }

                return $return instanceof Closure ? $return($value) : $return;
            }
        )->filter();
    }
}
