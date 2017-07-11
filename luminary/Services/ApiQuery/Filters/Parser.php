<?php

namespace Luminary\Services\ApiQuery\Filters;

class Parser
{
    /**
     * The accepted query types
     *
     * @var array
     */
    protected $filterTypes = ['and', 'or', 'nested', 'has'];

    /**
     * Parse a query array to return the
     * correct format for the filter queries
     *
     * @param array $query
     * @return array
     */
    public static function parse(array $query) :array
    {
        $parser = new static;

        return collect($parser->filter($query))
            ->map(
                function ($values, $key) use ($parser) {
                    $method = camel_case('parse_' . $key . '_query');
                    return $parser->{$method}($values);
                }
            )->toArray();
    }

    /**
     * Parse an `AND` query
     *
     * @param array $query
     * @return array
     */
    public function parseAndQuery(array $query)
    {
        return array_map(
            function (array $item) {
                return Composer::format('and', $item);
            },
            $query
        );
    }

    /**
     * Parse a `has` query
     *
     * @param array $query
     * @return array
     */
    public function parseHasQuery(array $query)
    {
        return array_map(
            function ($values, $key) {
                $values = static::parse($values);
                return Composer::formatHas($key, $values);
            },
            $query,
            array_keys($query)
        );
    }

    /**
     * Parse a `nested` query
     *
     * @param array $query
     * @return array
     */
    public function parseNestedQuery(array $query)
    {
        $query = $this->filter($query);

        return array_map(
            function ($values, $key) {
                $values = static::parse($values);
                return Composer::formatNested($key, $values);
            },
            $query,
            array_keys($query)
        );
    }

    /**
     * Parse an `OR` query
     *
     * @param array $query
     * @return array
     */
    public function parseOrQuery(array $query)
    {
        return array_map(
            function (array $item) {
                return Composer::format('or', $item);
            },
            $query
        );
    }

    /**
     * Get the list of query types
     *
     * @param array $except
     * @return array
     */
    public function getQueryTypes(array $except = []) :array
    {
        return empty($except) ? $this->filterTypes : $this->getQueryTypesExcept($except);
    }

    /**
     * Return a subset of the query types
     *
     * @param array $except
     * @return array
     */
    public function getQueryTypesExcept(array $except) :array
    {
        $types = array_flip($this->getQueryTypes());
        $except = array_except($types, $except);

        return array_values(array_flip($except));
    }

    /**
     * Return and associated index query
     * filtered by query types
     *
     * @param array $query
     * @return array
     */
    public function filter(array $query) :array
    {
        return array_only(
            $this->associateIndexedQueries($query),
            $this->getQueryTypes()
        );
    }

    /**
     * Return only the indexed items within an array
     *
     * @param array $query
     * @return array
     */
    public function filterIndexed(array $query) :array
    {
        return collect($query)->filter(
            function ($f, $key) {
                return is_numeric($key);
            }
        )->toArray();
    }

    /**
     * Associate any indexed queries within an
     * array to be an 'and' query
     *
     * @param array $query
     * @return array
     */
    public function associateIndexedQueries(array $query) :array
    {
        // Turn original filters into a collection
        $query = collect($query);

        // Get the filtered array
        $filtered = $this->filterIndexed($query->all());

        // If we have filtered content.
        // get and merge the 'and' query collection
        // with the filtered array
        if (! empty($filtered)) {
            $and = collect($query->get('and', []))->merge($filtered);
            $query->put('and', $and->toArray());
        }

        $sort = function ($value, $key) {
            return $key;
        };

        // return the updated filter array
        return $query->except(array_keys($filtered))->sortBy($sort)->toArray();
    }
}