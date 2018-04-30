<?php

namespace Luminary\Services\Timezone;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

trait TimezoneModelTrait
{
    /**
     * The timezone to convert to
     *
     * @var string
     */
    public static $timezone;

    /**
     * Set the Auth User Timezone
     *
     * @return void
     */
    public static function setAuthUserTimezone()
    {
        if($user = Auth::user()) {
            static::setTimezone($user->timezone);
        }
    }

    /**
     * Return the timezone property
     *
     * @return string|null
     */
    public static function getTimezone()
    {
        return static::$timezone;
    }

    /**
     * Clear the timezone property
     *
     * @return void
     */
    public static function clearTimezone() :void
    {
        static::setTimezone();
    }

    /**
     * Set the timezone property
     *
     * @param string|null $timezone
     * @return void
     */
    public static function setTimezone(string $timezone = null) :void
    {
        static::$timezone = $timezone;
    }

    /**
     * Return a Carbon instance
     * with the timezone set
     *
     * @param string|Carbon $timestamp
     * @return Carbon
     */
    public function convertToTimezone($timestamp) :Carbon
    {
        switch (true) {
            case is_null(static::$timezone):
                $return =  $timestamp;
                break;
            case $timestamp instanceof Carbon:
                $return =  $timestamp->setTimezone(static::$timezone);
                break;
            default:
                $return =  $this->asDateTime($timestamp)->setTimezone(static::$timezone);
        }

        return $return;
    }
}
