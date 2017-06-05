<?php

namespace Luminary\Services\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractFilesystem
{
    /**
     * Path to store/retrieve files
     *
     * @var string
     */
    protected static $path = '';

    /**
     * Default storage option
     *
     * @var string
     */
    protected static $type = 'local';

    /**
     * Import a new file and return its storage path
     *
     * @param string $filename
     * @param UploadedFile $file
     * @return string | bool
     */
    public static function upload($filename, UploadedFile $file)
    {
        $storage = static::getStorage();
        $path = static::getFullPath($filename);

        $stored = $storage->put($path, file_get_contents($file));

        static::clean($file);

        return $stored ? $filename : false;
    }

    /**
     * Get the Storage Adapter
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected static function getStorage()
    {
        return app('filesystem')->disk(static::$type);
    }

    /**
     * Get the storage client
     *
     * @return mixed
     */
    protected static function getClient()
    {
        return static::getStorage()
            ->getDriver()
            ->getAdapter()
            ->getClient();
    }

    /**
     * Get the full path for the file object
     *
     * @param string $filename
     * @return string
     */
    protected static function getFullPath($filename)
    {
        $path = static::$path . $filename;
        $explode = explode('/', $path);
        $filtered = array_filter($explode);

        return implode('/', $filtered);
    }

    /**
     * Remove temp file from local storage
     *
     * @param UploadedFile $file
     */
    protected static function clean(UploadedFile $file)
    {
        if ($file && file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Route static method calls to the filesystem instance
     *
     * @param string $name
     * @param array $arguments
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::getStorage(), $name], $arguments);
    }
}
