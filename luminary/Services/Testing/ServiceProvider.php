<?php

namespace Luminary\Services\Testing;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use Luminary\Services\Testing\Authorizers\CustomerAuthorize;
use Luminary\Services\Testing\Authorizers\LocationAuthorize;
use Luminary\Services\Testing\Sanitizers\LocationSanitizer;
use Luminary\Services\Testing\Validators\CustomerCreate;
use Luminary\Services\Testing\Sanitizers\CustomerSanitizer;
use Luminary\Services\Testing\Validators\CustomerUpdate;
use Luminary\Services\Testing\Validators\LocationCreate;
use Luminary\Services\Testing\Validators\LocationUpdate;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (app()->environment() == 'testing') {
            $this->registerTesting();
        }
    }

    /**
     * Register the testing database migrations
     * and factories
     *
     * @return void
     */
    public function registerTesting()
    {
        $app = $this->app;
        $dir = __DIR__ . '/database';

        // Register Factory
        app(ModelFactory::class)->load($dir . '/factories');

        // Register Migrations
        $app->afterResolving('migrator', function ($migrator) use ($dir) {
            $migrator->path($dir . '/migrations');
        });

        // Load Routes
        $this->loadRoutes();

        // Authorizers
        $app->authorizers('customers', CustomerAuthorize::class);
        $app->authorizers('locations', LocationAuthorize::class);

        // Sanitizers
        $app->sanitizers('customers', CustomerSanitizer::class);
        $app->sanitizers('locations', LocationSanitizer::class);

        // Validators
        $app->validators('customers', 'store', CustomerCreate::class);
        $app->validators('customers', 'update', CustomerUpdate::class);
        $app->validators('locations', 'store', LocationCreate::class);
        $app->validators('locations', 'update', LocationUpdate::class);
    }

    /**
     * Load the test routes
     *
     * @return void
     */
    public function loadRoutes()
    {
        app()->bootstrapRouter();
        app()->router->group(['middleware' => ['request', 'response']], function ($router) {
            require __DIR__ . '/routes.php';
        });
    }
}
