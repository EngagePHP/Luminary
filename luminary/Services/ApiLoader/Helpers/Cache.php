<?php

namespace Luminary\Services\ApiLoader\Helpers;

use Luminary\Services\Filesystem\Local\Storage;

class Cache
{
    /**
     * The filename for the loader cache
     *
     * @var string
     */
    protected static $file = 'api/loader-cache.php';

    /**
     * Clear the api loader cache
     *
     * @return void
     */
    public static function clear() :void
    {
        Storage::delete(static::$file);
    }

    /**
     * Create the api loader cache
     * from the artisan boot as a serialized
     * array
     *
     * @return void
     */
    public static function create() :void
    {
        $registry = app('api.loader')->registry()->toArray();
        $cache = serialize($registry);

        Storage::put(static::$file, $cache);
    }

    /**
     * Does the loader cache file exist?
     *
     * @return bool
     */
    public static function exists() :bool
    {
        return Storage::exists(static::$file);
    }

    /**
     * Return the loader cache as
     * an unserialized array
     *
     * @return array
     */
    public static function get() :array
    {
        $cache = Storage::get(static::$file);

        return unserialize($cache);
    }
}
