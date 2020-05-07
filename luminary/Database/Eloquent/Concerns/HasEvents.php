<?php

namespace Luminary\Database\Eloquent\Concerns;

use Illuminate\Support\Collection;

trait HasEvents
{
    /**
     * Add static observable events
     *
     * @var array
     */
    protected static $staticObservables = [];

    /**
     * A list of Model Observer classes
     * that have been initialized
     *
     * @var array
     */
    protected static $observedModelClasses = [];

    /**
     * Register an observer with the Model.
     *
     * @param  object|string  $class
     * @return void
     */
    public static function observe($class)
    {
        $instance = new static;
        $modelClass = get_class($instance);
        $observerClass = is_string($class) ? $class : get_class($class);

        if($instance->hasObservedModelClass($modelClass, $observerClass)) {
            return;
        }

        parent::observe($class);
        $instance->addObservedClass($modelClass, $observerClass);
    }

    /**
     * Get the observed model classes
     *
     * @param string|null $modelClass
     * @return Collection|array
     */
    protected function getObservedModelClasses(string $modelClass = null)
    {
        if(!static::$observedModelClasses instanceof Collection) {
            static::$observedModelClasses = new Collection;
        }

        return $modelClass ? static::$observedModelClasses->get($modelClass, []) : static::$observedModelClasses;
    }

    /**
     * Add a model and observed class to the
     * observedModelClass Collection
     *
     * @param string $modelClass
     * @param string $observerClass
     */
    protected function addObservedClass(string $modelClass, string $observerClass)
    {
        $observed = $this->getObservedModelClasses($modelClass);
        $observed[] = $observerClass;

        static::$observedModelClasses->put($modelClass, array_unique($observed));
    }

    /**
     * Check if a model has an observer
     * already set
     *
     * @param string $modelClass
     * @param string $observerClass
     * @return bool
     */
    protected function hasObservedModelClass(string $modelClass, string $observerClass): bool
    {
        $observers = $this->getObservedModelClasses($modelClass);
        return array_search($observerClass, $observers) !== false;
    }

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