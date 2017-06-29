<?php

namespace Luminary\Services\ApiQuery;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QueryScope implements Scope
{
    /**
     * The Api Query instance
     *
     * @var \Luminary\Services\ApiQuery\Query
     */
    protected $query;

    /**
     * The namespace within the query instance
     *
     * @var string
     */
    protected $namespace;

    /**
     * The eloquent model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The eloquent builder instance
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * QueryScope constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Apply scopes available to parent
        $this->applyScope($builder, $model);
        $this->eagerLoad($builder, $model);
        $this->scopePagination();
    }

    /**
     * Apply the query scopes
     *
     * @param object $builder
     * @param Model $model
     * @return void
     */
    public function applyScope($builder, Model $model) :void
    {
        $this->model = $model;
        $this->builder = $builder;

        $this->scopeFields();
        $this->scopeFilters();
        $this->scopeSorting();
    }

    /**
     * Get the current namespace
     *
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set the namespace property
     *
     * @param string $namespace
     * @return QueryScope
     */
    public function setNamespace(string $namespace) :QueryScope
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get the query instance
     *
     * @return Query
     */
    public function getQuery() :Query
    {
        return $this->query;
    }

    /**
     * Get the resource name for the
     * query instance
     *
     * @return string
     */
    public function resource() :string
    {
        return $this->getNamespace() ?: $this->query->resource();
    }

    /**
     * Eager load included relationships
     * and set their namespace within
     * the QueryScope instance
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function eagerLoad(Builder $builder, Model $model) :void
    {
        (new Includes\Scope($this))->apply($builder, $model);
    }

    /**
     * Return only the selected fields
     * from the query
     *
     * @return void
     */
    protected function scopeFields() :void
    {
        (new Fields\Scope($this))->apply($this->builder, $this->model);
    }

    /**
     * Filter the query
     *
     * @return void
     */
    protected function scopeFilters() :void
    {
        (new Filters\Scope($this))->apply($this->builder, $this->model);
    }

    /**
     * Return only the selected fields
     * from the query
     *
     * @return void
     */
    protected function scopePagination() :void
    {
        (new Pagination\Scope($this))->apply($this->builder, $this->model);
    }

    /**
     * Sort the results
     *
     * @return void
     */
    protected function scopeSorting() :void
    {
        (new Sorting\Scope($this))->apply($this->builder, $this->model);
    }
}
