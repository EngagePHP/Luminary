<?php

namespace Luminary\Services\ApiRequest;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Luminary\Application;

class ServiceProvider extends LaravelServiceProvider
{
    use Traits\HasCustomRequest;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * Anytime the default Request class is called from the
     * IOC Container, replace it with the Api Request Instance
     *
     * @return void
     */
    public function boot()
    {
        // Resolve the current request instance for Illuminate\Http\Request
        $this->app->resolving(Request::class, function ($request, Application $app) {
            $app->instance(Request::class, $request);
        });

        // Resolve the current request instance for ApiRequest
        $this->app->resolving(ApiRequest::class, function ($request, Application $app) {
            $app->instance(ApiRequest::class, $request);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->middleware([
            \Luminary\Services\ApiRequest\Middleware\SetApiRequest::class,
        ]);
    }
}
