<?php

namespace Luminary\Services\ApiQuery\Grouping;

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
        $group = $this->grouping();

        if(!empty($group)) {
            $builder->groupBy($group);
        }
    }

    /**
     * Get the query fields
     *
     * @return array
     */
    protected function grouping() :array
    {
        $resource = $this->resource();
        return $this->query()->grouping($resource);
    }
}
