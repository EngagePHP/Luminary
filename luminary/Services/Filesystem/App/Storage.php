<?php

namespace Luminary\Services\Filesystem\App;

use Luminary\Services\Filesystem\AbstractFilesystem;
use Symfony\Component\Routing\Generator\UrlGenerator;

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
     * @param string|array $path
     * @param bool $recursive
     * @return bool
     */
    public static function makeDirectory($path, bool $recursive = false) :bool
    {
        if (is_array($path)) {
            foreach ($path as &$p) {
                static::makeDirectory($p, $recursive);
            }

            return true;
        }

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

        if (!static::exists($path)) {
            static::put($path, "");
        }

        return $path;
    }

    /**
     * Create a relative link to the target file or directory.
     *
     * @param  string  $target
     * @param  string  $link
     * @return string
     */
    public static function link($target, $link) :string
    {
        if (! is_link($target)) {
            $current = getcwd();
            $directory = dirname($target);
            $name = basename($target);
            $relativePath = UrlGenerator::getRelativePath($target, $link);

            chdir($directory);

            static::getStorage()->link($relativePath, $name);

            chdir($current);
        }

        return readlink($target);
    }
}
