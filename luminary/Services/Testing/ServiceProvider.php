<?php

namespace Luminary\Services\Testing;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Database\Eloquent\Factory as ModelFactory;

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
        $dir = __DIR__ . '/database';

        // Register Factory
        app(ModelFactory::class)->load($dir . '/factories');

        // Register Migrations
        $this->app->afterResolving('migrator', function ($migrator) use ($dir) {
            $migrator->path($dir . '/migrations');
        });
    }
}
