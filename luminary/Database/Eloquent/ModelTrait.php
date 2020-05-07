<?php

namespace Luminary\Database\Eloquent;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Luminary\Database\Eloquent\Concerns\HasEvents;
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
    use HasEvents, PivotEventTrait {
        HasEvents::getObservableEvents as LuminaryObservableEvents;
        PivotEventTrait::getObservableEvents as PivotObservableEvents;
    }

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

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_unique(
            array_merge(
                $this->PivotObservableEvents(),
                $this->LuminaryObservableEvents()
            )
        );
    }

    /**
     * Register a expired model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function booting($callback)
    {
        static::registerModelEvent('booting', $callback);
    }

    /**
     * Register a expired model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function booted($callback)
    {
        static::registerModelEvent('booted', $callback);
    }
}
