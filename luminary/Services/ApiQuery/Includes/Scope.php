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
        $includes = collect($this->includes())->flip()->map(
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

    /**
     * Get the includes formatted for Query
     *
     * @return array
     */
    protected function includes() :array
    {
        $query = $this->scope->getQuery();

        return collect($query->includes())
            ->map(function ($include) {
                return $this->formatInclude($include);
            })->toArray();
    }

    /**
     * Format the include keys as camelCase
     *
     * @param string $include
     * @return string
     */
    protected function formatInclude(string $include) :string
    {
        $map = array_map(
            function ($part) {
                return camel_case($part);
            },
            explode('.', $include)
        );

        return implode('.', $map);
    }
}
