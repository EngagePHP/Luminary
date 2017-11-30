<?php

namespace Luminary\Services\ApiLoader\Loaders;

use Luminary\Services\ApiLoader\Registry\Registrar;

class DefaultLoader extends AbstractApiLoader
{
    /**
     * Load the directories and register
     * with laravel
     *
     * @param string $path
     */
    public function load(string $path)
    {
        $directory = $this->directory($path);
        $this->registrar->setDirectory($directory);

        $this->register($this->registrar);
    }

    /**
     * Return the relative path to the directory
     * for auto loading
     *
     * @return string
     */
    public static function path() :string
    {
        return '../database';
    }

    /**
     * Initialize a service path by name
     * to include
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registrar $registrar
     */
    protected function register(Registrar $registrar)
    {
        $registrar->registerMigrations('migrations');
    }
}
