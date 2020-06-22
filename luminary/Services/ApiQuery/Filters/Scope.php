<?php

namespace Luminary\Services\ApiQuery\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;

class Scope extends BaseScope
{
    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param Model $model
     * @param null $filters
     * @return void
     */
    public function apply($builder, Model $model, $filters = null): void
    {
        $this->builder = $builder;
        $this->model = $model;
        $filters = !is_null($filters) ? $filters : $this->filters();

        collect($filters)->each(
            function ($filter, $type) {
                $method = camel_case('apply_' . $type . '_queries');
                return $this->{$method}($filter);
            }
        );
    }

    /**
     * Apply `AND` query filters
     *
     * @param array $filters
     * @return void
     */
    protected function applyAndQueries(array $filters): void
    {
        collect($filters)->each(
            function ($filter) {
                $this->applyAndQuery($filter);
            }
        );
    }

    /**
     * Apply `AND` query filter
     *
     * @param array $query
     * @return void
     */
    protected function applyAndQuery(array $query): void
    {
        $this->applyFilter(...array_values($query));
    }

    /**
     * Apply `between` query filters
     *
     * @param array $filters
     * @param string $boolean
     * @return void
     */
    protected function applyBetweenQueries(array $filters, $boolean = 'and'): void
    {
        collect($filters)->each(
            function ($filter) use ($boolean) {
                $this->applyBetweenQuery($filter, $boolean);
            }
        );
    }

    /**
     * Apply `Between` query filter
     *
     * @param array $query
     * @param string $boolean
     * @return void
     */
    protected function applyBetweenQuery(array $query, $boolean = 'and'): void
    {
        $value = array_get($query, 'value');
        $column = array_shift($value);

        strtotime($column)
            ? $this->whereBetweenRaw(array_get($query, 'value'), $boolean)
            : $this->builder->whereBetween($column, $value, $boolean);
    }

    /**
     * Apply `between` query filters
     *
     * @param array $query
     * @return void
     */
    protected function applyOrBetweenQueries(array $query): void
    {
        $this->applyBetweenQueries($query, 'or');
    }

    /**
     * Apply a where between raw query
     *
     * @param array $bindings
     * @param string $boolean
     */
    protected function whereBetweenRaw(array $bindings, $boolean = 'and')
    {
        $embed = [];

        $bindings = collect($bindings)
            ->filter(
                function($binding) use(&$embed) {
                    $time = (strtotime($binding));
                    $embed[] = $time ? '?' : '`' . $this->table() . '`.`' . $binding . '`';

                    return $time;
                }
            )->all();

        list($a,$b,$c) = $embed;

        $this->builder->whereRaw("$a between $b and $c", $bindings, $boolean);
    }

    /**
     * Apply `intersect` query filters
     *
     * @param array $filters
     * @param string $boolean
     * @return void
     */
    protected function applyIntersectQueries(array $filters, $boolean = 'and'): void
    {
        collect($filters)->each(
            function ($filter) use ($boolean) {
                $this->applyIntersectQuery($filter, $boolean);
            }
        );
    }

    /**
     * Apply the intersect query
     *
     * @param array $query
     * @param string $boolean
     */
    protected function applyIntersectQuery(array $query, $boolean = 'and'): void
    {
        $values = array_get($query, 'value');
        $closure = function($query) use ($values) {
            list($intersectStart, $intersectEnd, $columnStart, $columnEnd) = $values;

            //(`start_at` <= @end AND `end_at` >= @start)
            $query->where([
                [$columnStart, '<=', $intersectEnd],
                [$columnEnd, '>=', $intersectStart]
            ])
            // OR (`start_at` >= @end AND `start_at` <= @start AND `end_at` <= @start)
            ->orWhere([
                [$columnStart, '>=', $intersectEnd],
                [$columnStart, '<=', $intersectStart],
                [$columnEnd, '<=', $intersectStart]
            ])
            //OR (`end_at` <= @start AND `end_at` >= @end AND `start_at` <= @end)
            ->orWhere([
                [$columnEnd, '<=', $intersectStart],
                [$columnEnd, '>=', $intersectEnd],
                [$columnStart, '<=', $intersectEnd]
            ])
            //OR (start_at >= @end AND start_at <= @start)
            ->orWhere([
                [$columnStart, '>=', $intersectEnd],
                [$columnStart, '<=', $intersectStart]
            ]);
        };

        $this->builder->where($closure, null, null, $boolean);
    }

    /**
     * Apply `Nested` query filters
     *
     * @param array $filters
     * @param string $boolean
     * @return void
     */
    protected function applyNestedQueries(array $filters, $boolean = 'and'): void
    {
        collect($filters)->each(
            function ($filter) use($boolean) {
                $this->applyNestedQuery($filter, $boolean);
            }
        );
    }

    /**
     * Apply `Nested` query filters
     *
     * @param array $filters
     * @return void
     */
    protected function applyOrNestedQueries(array $filters): void
    {
        $this->applyNestedQueries($filters, 'or');
    }

    /**
     * Apply `Nested` query filter
     *
     * @param array $query
     * @param string $boolean
     * @return void
     */
    protected function applyNestedQuery(array $query, $boolean = 'and'): void
    {
        $bool = array_get($query, 'attribute');
        $queries = array_get($query, 'value');
        $closure = function ($query) use ($queries) {
            $scope = new static($this->scope);
            $scope->apply($query, $query->getModel(), $queries);
        };
        $args = [$closure, null, null, $bool];

        $boolean == 'or' ? $this->builder->orWhere(...$args):  $this->builder->where(...$args);
    }

    /**
     * Apply `OR` query filters
     *
     * @param array $filters
     * @return void
     */
    protected function applyOrQueries(array $filters): void
    {
        collect($filters)->each(
            function ($filter) {
                $this->applyOrQuery($filter);
            }
        );
    }

    /**
     * Apply `OR` query filter
     *
     * @param array $query
     * @return void
     */
    protected function applyOrQuery(array $query): void
    {
        $this->applyFilter(...array_values($query));
    }

    /**
     * Apply the query filter
     *
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @param string $type
     * @return void
     */
    protected function applyFilter(string $attribute, string $operator, $value, string $type): void
    {
        $builder = $this->builder;
        $columns = $this->getQueryColumns($builder);
        $attribute = $this->getFullyQualifiedColumn($columns, $attribute);

        switch ($operator) {
            case 'IN':
                $builder->whereIn($attribute, $value, $type);
                break;
            case 'NOT IN':
                $builder->whereNotIn($attribute, $value, $type);
                break;
            case 'NULL':
                $builder->whereNull($attribute, $type);
                break;
            case 'NOT NULL':
                $builder->whereNotNull($attribute, $type);
                break;
            default:
                $builder->where($attribute, $operator, $value, $type);
                break;
        }
    }

    /**
     * Get the fully qualified column name from
     * an array of query columns and field name
     *
     * @param array $columns
     * @param string $field
     * @return string
     */
    public function getFullyQualifiedColumn(array $columns, string $field): string
    {
        $column = collect($columns)->first(
            function ($column) use ($field) {

                if (preg_match('/ as '.$field.'/i', $column)) {
                    return true;
                }

                if (preg_match('/'.$field.'/i', $column) && strpos($column, '.')) {
                    $c = strtok($column, '.');

                    return (str_replace($c . '.', '', $column) === $field);
                }

                return false;
            }
        );

        return $column ? head(explode(' as', $column)) :  $this->table() . '.' . $field;
    }

    /**
     * Get the query column array
     * from builder
     *
     * @param $builder
     * @return array
     */
    public function getQueryColumns($builder): array
    {
        if ($builder instanceof Relation) {
            $builder = $builder->getQuery();
        }

        return $builder->getQuery()->columns ?: [];
    }

    /**
     * Return the includes for the current
     * resource
     *
     * @return array
     */
    protected function filters(): array
    {
        $resource = $this->resource();
        $resource = snake_case($resource);

        return collect($this->query()->filters($resource))->except('has')->toArray();
    }
}
