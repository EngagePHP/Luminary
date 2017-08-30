<?php

namespace Luminary\Services\ApiLoader\Loaders;

use Luminary\Services\ApiLoader\Registry\Registrar;

class ResourceLoader extends AbstractApiLoader
{
    /**
     * Return the relative path to the directory
     * for auto loading
     *
     * @return string
     */
    public static function path() :string
    {
        return 'Resources';
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
        $registrar->registerRoutes('routes.php');
        $registrar->registerRouteMiddleware('Middleware/registry.php');
        $registrar->registerAuthorizer('Requests/Auth.php');
        $registrar->registerSanitizer('Requests/Sanitizer.php');
        $registrar->registerValidators('Requests/Validators');
    }
}
