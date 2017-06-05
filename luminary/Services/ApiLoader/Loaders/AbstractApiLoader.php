<?php

namespace Luminary\Services\ApiLoader\Loaders;

use Illuminate\Support\Collection;
use Luminary\Services\ApiLoader\Registry\Registrar;
use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\ApiLoader\Helpers\Directory;

abstract class AbstractApiLoader
{
    /**
     * The Registrar Instance
     *
     * @var \Luminary\Services\ApiLoader\Registry\Registrar
     */
    protected $registrar;

    /**
     * AbstractApiLoader constructor.
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registrar $registrar
     */
    public function __construct(Registrar $registrar)
    {
        $this->registrar = $registrar;
    }

    /**
     * Load the directories and register
     * with laravel
     *
     * @param string $path
     */
    public function load(string $path)
    {
        $this->directories($path)->each(function ($directory) {
            $directory = $this->directory($directory);
            $this->registrar->setDirectory($directory);

            $this->register($this->registrar);
        });
    }

    /**
     * Get directories by path
     *s
     * @return \Illuminate\Support\Collection
     */
    protected function directories(string $path) : Collection
    {
        $directories = Storage::directories($path) ?: [];
        return collect($directories);
    }

    /**
     * Create a new Directory instance
     * by path
     *
     * @param string $path
     * @return \Luminary\Services\ApiLoader\Helpers\Directory
     */
    protected function directory(string $path)
    {
        return Directory::make($path);
    }

    /**
     * Return the relative path to the directory
     * for auto loading
     *
     * @return string
     */
    abstract public static function path() :string;

    /**
     * Register a directory with Laravel
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registrar $registrar
     */
    abstract protected function register(Registrar $registrar);
}
