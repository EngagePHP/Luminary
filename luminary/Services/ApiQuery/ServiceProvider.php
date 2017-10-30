<?php

namespace Luminary\Services\ApiQuery;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(ActivatesWhenResolvedTrait::class, function ($resolved) {
            $resolved->activate();
        });
    }

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
    }
}
