<?php

namespace Luminary\Models\Expire;

trait ExpireModelTrait
{
    /**
     * Bool to trigger the
     * expired model event
     *
     * @var bool
     */
    public $expired = false;

    /**
     * Bool to trigger the
     * unexpired model event
     *
     * @var bool
     */
    public $unexpired = false;

    /**
     * Bool to keep observer
     * from double booting
     *
     * @var bool
     */
    protected static $expireObserved = false;

    /**
     * Set the expired observer
     *
     * @return void
     */
    public static function bootExpireModelTrait()
    {
        if(! static::$expireObserved) {
            static::observe(ExpireObserver::class);
            static::$expireObserved = true;
        }

        static::setModelExpiredScope();
    }

    /**
     * Scope the relationship attributes as part of the Model
     *
     * @return void
     */
    protected static function setModelExpiredScope() :void
    {
        static::addGlobalScope(new ExpireModelScope);
    }

    /**
     * Set the Expired Observables
     */
    public static function setExpiredObservables()
    {
        $observables = [
            'expiring',
            'expired',
            'unexpiring',
            'unexpired'
        ];

        static::addStaticObservableEvents($observables);
    }

    /**
     * Register a expiring model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function expiring($callback)
    {
        static::registerModelEvent('expiring', $callback);
    }

    /**
     * Register a expired model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function expired($callback)
    {
        static::registerModelEvent('expired', $callback);
    }

    /**
     * Register a expiring model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unexpiring($callback)
    {
        static::registerModelEvent('unexpiring', $callback);
    }

    /**
     * Register a expired model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unexpired($callback)
    {
        static::registerModelEvent('unexpired', $callback);
    }

    /**
     * Get the name of the "expired at" column.
     *
     * @return string
     */
    public function getExpiredAtColumn()
    {
        return defined('static::EXPIRED_AT') ? static::EXPIRED_AT : 'expired_at';
    }

    /**
     * Get the fully qualified "expired at" column.
     *
     * @return string
     */
    public function getQualifiedExpiredAtColumn()
    {
        return $this->qualifyColumn($this->getExpiredAtColumn());
    }

    /**
     * Trigger the expired event
     *
     * @return void
     */
    public function triggerExpiring()
    {
        if ($this->fireModelEvent('expiring') !== false) {
            $this->expired = true;
        }
    }

    /**
     * Trigger the unexpired event
     *
     * @return void
     */
    public function triggerUnexpiring()
    {
        if ($this->fireModelEvent('unexpiring') !== false) {
            $this->unexpired = true;
        }
    }

    /**
     * Trigger the expired event
     *
     * @return void
     */
    public function triggerExpired()
    {
        if ($this->fireModelEvent('expired') !== false) {
            $this->expired = true;
        }
    }

    /**
     * Trigger the unexpired event
     *
     * @return void
     */
    public function triggerUnexpired()
    {
        if ($this->fireModelEvent('unexpired') !== false) {
            $this->unexpired = true;
        }
    }
}