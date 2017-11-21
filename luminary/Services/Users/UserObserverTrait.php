<?php

namespace Luminary\Services\Users;

trait UserObserverTrait
{
    /**
     * Set the user observer
     *
     * @return void
     */
    public static function bootUserObserverTrait()
    {
        static::observe(UserObserver::class);
    }
}
