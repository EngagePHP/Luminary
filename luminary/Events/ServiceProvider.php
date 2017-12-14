<?php

namespace Luminary\Events;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EventMapper::class, function () {
            return new EventMapper;
        });

        $this->app->routeMiddleware([
            'hook' => EventHookMiddleware::class,
        ]);

        $this->registerRoutes();
    }

    /**
     * Register the Event Routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $file = realpath(__DIR__ . '/routes.php');
        app('api.loader')->loadRoutesFrom($file);
    }
}
