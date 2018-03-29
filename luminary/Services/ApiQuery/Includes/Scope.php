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

        $includes = collect($query->includes())->map(
            function ($include) {
                return camel_case($include);
            }
        )->flip()->map(
            function ($i, $include) {
                return $this->mapIncludes($include);
            }
        )->toArray();

        $builder->with($includes);
    }

    /**
     * Apply the main query scope to the
     * sub query
     *
     * @param $builder
     * @param Model $model
     * @param string $include
     * @return void
     */
    protected function applyScope($builder, Model $model, string $include)
    {
        $scope = $this->scope;
        $namespace = $scope->getNamespace();

        $scope->setNamespace($include)
            ->applyScope($builder, $model);

        $scope->setNamespace($namespace);
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
            $this->applyScope($builder, $model, $include);
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
