<?php

namespace Luminary\Services\ApiQuery;

use Luminary\Services\ApiQuery\Eloquent\Builder;
use Spatie\Permission\Models\Role;

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
        $isParent = static::isParent();

        if ($apiQuery->isActive() && $isParent) {
            static::addGlobalScope(new QueryScope($apiQuery, $isParent));
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
     * Check whether the model is the parent
     * model excluding specific models
     *
     * @return bool
     */
    public static function isParent()
    {
        $booted = array_except(
            static::$booted, [
                Role::class
            ]
        );

        return count($booted) === 1;
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
