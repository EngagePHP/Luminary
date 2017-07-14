<?php

namespace Luminary\Services\ApiQuery\Includes;

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
        $query = $this->scope->getQuery();

        $includes = collect($query->includes())->flip()->map(
            function ($i, $include) {
                return $this->mapIncludes($include);
            }
        )->toArray();

        $builder->with($includes);
    }

    /**
     * Return the closure for mapping an
     * individual include
     *
     * @return \Closure
     */
    protected function mapIncludes($include)
    {
        return function ($builder) use ($include) {
            $model = $builder->getModel();

            $this->scope->setNamespace($include)
                ->applyScope($builder, $model);
        };
    }
}
