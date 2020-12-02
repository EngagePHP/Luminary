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
     * @param string|null $namespace
     */
    public function __construct(Query $query, string $namespace = null)
    {
        $this->query = $query;
        $this->namespace = $namespace;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply($builder, Model $model)
    {
        $this->builder = $builder;
        $this->model = $model;

        // Apply scopes available to parent
        $this->scopeWith();
        $this->scopeOnly();
        $this->applyScope($builder, $model);
        $this->eagerLoad($builder, $model);
        $this->scopeHasFilters();
        $this->scopeSearch();
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
        $this->scopeGrouping();
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
        $this->namespace = str_replace(['-', ' '], '_', $namespace);

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
        $namespace = $this->getNamespace();

        if(is_null($namespace)) {
            $namespace = $this->query->resource();
            $this->setNamespace($namespace);
        }

        return  $namespace;
    }

    /**
     * Eager load included relationships
     * and set their namespace within
     * the QueryScope instance
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function eagerLoad($builder, Model $model) :void
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
     * Filter the query
     *
     * @return void
     */
    protected function scopeHasFilters() :void
    {
        (new Filters\ScopeHas($this))->apply($this->builder, $this->model);
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
     * Return only the selected fields
     * from the query
     *
     * @return void
     */
    protected function scopeSearch() :void
    {
        (new Search\Scope($this))->apply($this->builder, $this->model);
    }

    /**
     * Add with Scopes to the query
     *
     * @return void
     */
    protected function scopeWith(): void
    {
        (new With\Scope($this))->apply($this->builder, $this->model);
    }

    /**
     * Add with Scopes to the query
     *
     * @return void
     */
    protected function scopeOnly(): void
    {
        (new Only\Scope($this))->apply($this->builder, $this->model);
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

    /**
     * Sort the results
     *
     * @return void
     */
    protected function scopeGrouping() :void
    {
        (new Grouping\Scope($this))->apply($this->builder, $this->model);
    }
}
