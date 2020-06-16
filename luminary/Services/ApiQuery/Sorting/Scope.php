<?php

namespace Luminary\Services\ApiQuery\Sorting;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;

class Scope extends BaseScope
{
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

        collect($this->sorting())->each(
            function ($sort, $column) use ($builder) {
                //$column = $this->table().'.'.$column;
                $builder->orderBy($column, $sort);
            }
        );
    }

    /**
     * Get the query fields
     *
     * @return array
     */
    protected function sorting() :array
    {
        $resource = str_replace('_', '-', $this->resource());
        return $this->query()->sorting($resource);
    }
}
