<?php

namespace Luminary\Services\ApiQuery\Filters;

use Illuminate\Database\Eloquent\Model;
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
    public function apply($builder, Model $model, $filters = null) :void
    {
        $this->builder = $builder;
        $this->model = $model;
        $filters = ! is_null($filters) ? $filters : $this->filters();

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
    protected function applyAndQueries(array $filters) :void
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
    protected function applyAndQuery(array $query) :void
    {
        $this->applyFilter(...array_values($query));
    }

    /**
     * Apply `Nested` query filters
     *
     * @param array $filters
     * @return void
     */
    protected function applyNestedQueries(array $filters) :void
    {
        collect($filters)->each(
            function ($filter) {
                $this->applyNestedQuery($filter);
            }
        );
    }

    /**
     * Apply `Nested` query filter
     *
     * @param array $query
     * @return void
     */
    protected function applyNestedQuery(array $query) :void
    {
        $boolean = array_get($query, 'attribute');
        $queries = array_get($query, 'value');
        $closure = function ($query) use ($queries) {
            $scope = new static($this->scope);
            $scope->apply($query, $query->getModel(), $queries);
        };

        $this->builder->where($closure, null, null, $boolean);
    }

    /**
     * Apply `OR` query filters
     *
     * @param array $filters
     * @return void
     */
    protected function applyOrQueries(array $filters) :void
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
    protected function applyOrQuery(array $query) :void
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
    protected function applyFilter(string $attribute, string $operator, $value, string $type) :void
    {
        $builder = $this->builder;
        $attribute = $this->formatAttribute($attribute);

        if ($this->isExpression($value)) {
            $this->applyExpressionFilter($attribute, $operator, $value, $type);
            return;
        }

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
     * Format an attribute with table name
     * if missing
     *
     * @param string $attribute
     * @return string
     */
    protected function formatAttribute(string $attribute) :string
    {
        return strpos($attribute, '.') !== true
            ? $this->model->getTable() . '.' . $attribute
            : $attribute;
    }

    /**
     * Check if the current value should be
     * called as an expression
     *
     * @param $value
     * @return bool
     */
    protected function isExpression($value) :bool
    {
        $explode = explode('.', $value);
        return count($explode) > 1;
    }

    /**
     * Create an expression from a value
     *
     * @param string $value
     * @return Expression
     */
    protected function createExpression(string $value) :Expression
    {
        $explode = explode('.', $value);

        return collect($explode)->map(
            function ($v) {
                return "\"$v\"";
            }
        )->pipe(
            function ($collection) {
                $implode = implode('.', $collection->all());
                return \DB::raw($implode);
            }
        );
    }

    /**
     * Apply and expression filter
     *
     * @param string $attribute
     * @param string $operator
     * @param $value
     * @param string $type
     */
    protected function applyExpressionFilter(string $attribute, string $operator, $value, string $type) :void
    {
        $expression = $this->createExpression($value);
        $this->builder->whereRaw("$attribute $operator " . $expression->getValue(), [], $type);
    }

    /**
     * Return the includes for the current
     * resource
     *
     * @return array
     */
    protected function filters() :array
    {
        $resource = $this->resource();
        return collect($this->query()->filters($resource))->except('has')->toArray();
    }
}
