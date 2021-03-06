<?php

namespace Luminary\Services\ApiQuery\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;
use Luminary\Services\ApiQuery\QueryArr;

class Scope extends BaseScope
{
    /**
     * Hold the current relationship foreign key
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param Model $model
     * @return void
     */
    public function apply($builder, Model $model) :void
    {
        $this->builder = $builder;
        $this->model = $model;
        $columns = $this->getQueryColumns($builder);

        $fields = collect($this->fields())->filter(function ($field) {
            return $field !== '*';
        })->map(
            function ($field) use ($columns) {
                return $this->getFullyQualifiedColumn($columns, $field);
            }
        );

        if ($fields->count()) {
            $builder->select($fields->all());
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
    public function getFullyQualifiedColumn(array $columns, string $field) :string
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

        return $column ? $column : $this->table() . '.' . $field;
    }

    /**
     * Get the query column array
     * from builder
     *
     * @param $builder
     * @return array
     */
    public function getQueryColumns($builder) :array
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
    protected function includes() :array
    {
        $query = $this->query()->includes();
        $includes = QueryArr::dotValue($query);

        return array_get($includes, $this->resource(), []);
    }

    /**
     * Get the query fields
     *
     * @return array
     */
    protected function fields()
    {
        $resource = str_replace('-', '_', $this->resource());
        $fields = $this->query()->fields($resource);
        $required = [];

        if ($fields !== ['*']) {
            $required = $this->requiredFields($fields);

            if (count($required)) {
                $this->addHiddenFields($required);
            }
        }

        return collect($fields)->merge($required)->unique()->toArray();
    }

    /**
     * Get a list of required fields
     * that were not passed in the fields
     * list
     *
     * @param array $fields
     * @return array
     */
    protected function requiredFields(array $fields) :array
    {
        $required = array_merge(
            [
                $this->model->getKeyName(),
                $this->getBuilderForeignKey(),
            ],
            $this->getEagerKeys()
        );

        return collect($required)->filter()->diff($fields)->toArray();
    }

    /**
     * Return a list of required keys for
     * included relationships
     *
     * @return array
     */
    protected function getEagerKeys() :array
    {
        $model = $this->model;

        return collect($this->includes())->map(
            function ($include) use ($model) {
                $builder = $model->{$include}();
                return $this->getForeignKey($builder);
            }
        )->toArray();
    }

    /**
     * Set the foreign key based
     * on relationship
     *
     * @param null $builder
     * @return mixed
     */
    public function getForeignKey($builder = null)
    {
        $key = null;

        switch (true) {
            case $builder instanceof BelongsTo:
                $key = $builder->getForeignKey();
                break;
            case $builder instanceof HasMany:
                $key = $builder->getForeignKeyName();
                break;
        }

        return $key;
    }

    /**
     * Get the foreign key for the
     * current builder instance
     *
     * @return mixed|null
     */
    public function getBuilderForeignKey()
    {
        $builder = $this->builder;

        // BelongsTo key is required
        // for the parent and not this
        // builder instance
        if ($builder instanceof BelongsTo) {
            return null;
        }

        return $this->getForeignKey($builder);
    }

    /**
     * Add the required fields to the hidden fields
     * list of a model
     *
     * @param array $fields
     */
    protected function addHiddenFields(array $fields)
    {
        $model = method_exists($this->builder, 'getRelated')
            ? $this->builder->getRelated()
            : $this->model;

        $model->addHidden(array_merge($fields));
    }
}
