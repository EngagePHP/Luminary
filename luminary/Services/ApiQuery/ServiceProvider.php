<?php

namespace Luminary\Services\ApiQuery;

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
        $this->app->singleton(Query::class, function () {
            return new Query(new QueryCollection);
        });

        $this->app->middleware(QueryMiddleware::class);

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
        $dir = __DIR__ . '/Testing/database';

        // Register Factory
        app(ModelFactory::class)->load($dir . '/factories');

        // Register Migrations
        $this->app->afterResolving('migrator', function ($migrator) use ($dir) {
            $migrator->path($dir . '/migrations');
        });
    }
}
