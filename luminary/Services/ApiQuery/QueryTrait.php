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

        if ($apiQuery->isActive() && static::isParent()) {
            static::addGlobalScope(new QueryScope($apiQuery));
        }
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
}
