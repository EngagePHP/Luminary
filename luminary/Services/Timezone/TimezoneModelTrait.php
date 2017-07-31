<?php

namespace Luminary\Services\Timezone;

use Carbon\Carbon;

trait TimezoneModelTrait
{
    /**
     * The timezone to convert to
     *
     * @var string
     */
    public static $timezone;

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
                return $timestamp;
                break;
            case $timestamp instanceof Carbon:
                return $timestamp->timezone(static::$timezone);
                break;
            default:
                return $this->asDateTime($timestamp)->timezone(static::$timezone);
        }
    }

    /**
     * Return a timestamp as DateTime object
     * with converted timezone
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        $value = parent::asDateTime($value);
        return $this->convertToTimezone($value);
    }
}
