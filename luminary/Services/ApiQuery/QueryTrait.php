<?php

namespace Luminary\Services\ApiQuery;

use Luminary\Services\ApiQuery\Eloquent\Builder;

trait QueryTrait
{
    /**
     * Boot the Http Query trait for a model.
     *
     * @return void
     */
    public static function bootQueryTrait()
    {
        $apiQuery = app(Query::class);

        if (static::shouldApplyApiQuery($apiQuery)) {
            static::applyApiQueryScope($apiQuery);
        }
    }

    /**
     * Check if the Api Query scopes should run
     *
     * @param Query $apiQuery
     * @return bool
     */
    public static function shouldApplyApiQuery(Query $apiQuery) :bool
    {
        return ($apiQuery->isActive() && static::isParent() && !$apiQuery->hasDynamicRouting());
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Luminary\Services\ApiQuery\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBootedModels()
    {
        // Remove the static pagination
        // On clear boot
        Builder::setPaginated(false);

        parent::clearBootedModels();
    }

    /**
     * Apply the query scope
     *
     * @param Query|null $query
     * @param string|null $namespace
     */
    public static function applyApiQueryScope(Query $query = null, string $namespace = null)
    {
        $query = $query ?: app(Query::class);
        static::addGlobalScope(new QueryScope($query, $namespace));
    }

    /**
     * Apply the related query scope
     *
     * @param $builder
     * @param Query|null $query
     * @param string|null $namespace
     */
    public function applyRelatedQueryScope($builder, Query $query = null, string $namespace = null)
    {
        $query = $query ?: app(Query::class);
        (new QueryScope($query, $namespace))->apply($builder, $this);
    }
}
