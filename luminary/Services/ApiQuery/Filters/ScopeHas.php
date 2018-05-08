<?php

namespace Luminary\Services\ApiQuery\Filters;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;
use Luminary\Services\ApiQuery\Filters\Scope as FilterScope;

class ScopeHas extends BaseScope
{
    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param Model $model
     * @param null $filters
     * @return void
     */
    public function apply($builder, Model $model = null, $filters = null) :void
    {
        $this->builder = $builder;
        $this->model = $model;

        $this->hasFilters()->each(function($query, $has) {
            $query ? $this->whereHas($has, $query) : $this->has($has);
        });
    }

    /**
     * Get the has Query
     *
     * @return \Illuminate\Support\Collection
     */
    protected function hasFilters()
    {
        return collect($this->query()->has());
    }

    /**
     * Add a has query to the builder
     *
     * @param string $has
     */
    protected function has(string $has)
    {
        $this->builder->has($has);
    }

    /**
     * Add a whereHas query to the builder
     *
     * @param string $has
     * @param array $query
     */
    protected function whereHas(string $has, array $query)
    {
        $this->builder->whereHas($has, function($builder) use($query) {
            (new FilterScope($this->scope))->apply($builder, $builder->getModel(), $query);
        });
    }
}
