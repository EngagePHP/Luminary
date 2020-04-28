<?php

namespace Luminary\Database\Eloquent;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\Relation;
use Luminary\Services\ApiQuery\QueryTrait;
use Luminary\Services\ApiResponse\ResponseModelTrait;
use Luminary\Services\Timezone\TimezoneModelTrait;
use Spatie\Permission\Models\Role;

trait ModelTrait
{
    use MorphTrait;
    use QueryTrait;
    use ResponseModelTrait;
    use TimezoneModelTrait;
    use PivotEventTrait;

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // @todo: find out the implementation for laravel 5.5
    }

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // @todo: find out the implementation for laravel 5.5
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
}
