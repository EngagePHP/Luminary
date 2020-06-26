<?php

namespace Luminary\Services\ApiLoader\Loaders;

use Luminary\Services\ApiLoader\Registry\Registrar;

class EntityLoader extends AbstractApiLoader
{
    /**
     * Return the relative path to the directory
     * for auto loading
     *
     * @return string
     */
    public static function path() :string
    {
        return 'Entities';
    }

    /**
     * Initialize a service path by name
     * to include
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registrar $registrar
     */
    protected function register(Registrar $registrar)
    {
        $registrar->registerModelFactories('Database/Factories');
        $registrar->registerMigrations('Database/Migrations');
        $registrar->registerMiddleware('Middleware');
        $registrar->registerPolicies('Policies/PolicyRegistrar.php');
        $registrar->registerSeeders('Database/Seeds');
        $registrar->registerEvents('Events/EventRegistrar.php');
        $registrar->registerMorphMap('Models');


        // Http
        $registrar->registerRoutes('Http/routes.php');
        $registrar->registerCustomRoutes('Http/custom-routes.php');
        $registrar->registerRouteMiddleware('Http/Middleware/registry.php');
        $registrar->registerAuthorizer('Http/Requests/Auth.php');
        $registrar->registerSanitizer('Http/Requests/Sanitizer.php');
        $registrar->registerValidators('Http/Requests/Validators');
    }
}
