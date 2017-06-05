<?php

namespace Luminary\Services\ApiLoader\Loaders;

use Luminary\Services\ApiLoader\Registry\Registrar;

class ServiceLoader extends AbstractApiLoader
{
    /**
     * Return the relative path to the directory
     * for auto loading
     *
     * @return string
     */
    public static function path() :string
    {
        return 'Services';
    }

    /**
     * Initialize a service path by name
     * to include
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registrar $registrar
     * @return void
     */
    protected function register(Registrar $registrar)
    {
        $registrar->registerConsole('Console');
        $registrar->registerProvider('ServiceProvider.php');
    }
}
