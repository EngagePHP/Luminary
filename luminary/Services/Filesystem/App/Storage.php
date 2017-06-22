<?php

namespace Luminary\Services\Filesystem\App;

use Luminary\Services\Filesystem\AbstractFilesystem;

class Storage extends AbstractFilesystem
{
    /**
     * Default storage option
     *
     * @var string
     */
    protected static $type;

    /**
     * Get the Storage Adapter
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected static function getStorage()
    {
        return app('files');
    }

    /**
     * Create a directory within the filesystem
     * recursively w/default permissions
     *
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    public static function makeDirectory(string $path, bool $recursive = false) :bool
    {
        return static::getStorage()->makeDirectory($path, 0755, true, $recursive);
    }

    /**
     * Create a .gitkeep file
     *
     * @param $path
     * @return string
     */
    public static function gitKeep($path) :string
    {
        $path = $path . '/.gitkeep';

        if (! static::exists($path)) {
            static::put($path, "");
        }

        return $path;
    }
}
