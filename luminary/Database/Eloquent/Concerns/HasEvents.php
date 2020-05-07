<?php

namespace Luminary\Database\Eloquent\Concerns;

trait HasEvents
{
    /**
     * Add static observable events
     *
     * @var array
     */
    protected static $staticObservables = [];



    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            parent::getObservableEvents(),
            static::$staticObservables
        );
    }

    /**
     * Add an observable event name.
     *
     * @param  array|mixed  $observables
     * @return void
     */
    public static function addStaticObservableEvents($observables)
    {
        static::$staticObservables = array_unique(array_merge(
            static::$staticObservables, is_array($observables) ? $observables : func_get_args()
        ));

        return;
    }
}