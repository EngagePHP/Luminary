<?php

namespace Luminary\Providers;

use Illuminate\Support\ServiceProvider;
use Luminary\Models\LuminaryModelServiceProvider;
use Luminary\Services\ApiQuery\ServiceProvider as ApiQueryServiceProvider;
use Luminary\Services\ApiRequest\ServiceProvider as ApiRequestServiceProvider;
use Luminary\Services\ApiResponse\ServiceProvider as ApiResponseServiceProvider;
use Luminary\Services\ApiSoftDeletes\ServiceProvider as ApiSoftDeletesServiceProvider;
use Luminary\Services\Auth\ServiceProvider as AuthServiceProvider;
use Luminary\EventHooks\ServiceProvider as EventServiceProvider;
use Luminary\Services\Testing\ServiceProvider as TestingServiceProvider;
use Luminary\Services\Filesystem\FilesystemServiceProvider;
use Luminary\Services\Routing\ServiceProvider as RoutingServiceProvider;
use Luminary\Services\FormRequests\ServiceProvider as FormReqeustsServiceProvider;
use Tymon\JWTAuth\Providers\LumenServiceProvider as JWTAuthServiceProvider;

class LuminaryServiceProvider extends ServiceProvider
{
    /**
     * A list of providers to register with
     * the Luminary application
     *
     * @var array
     */
    protected $providers = [
        FilesystemServiceProvider::class,
        FormReqeustsServiceProvider::class,
        ApiQueryServiceProvider::class,
        ApiRequestServiceProvider::class,
        ApiResponseServiceProvider::class,
        TestingServiceProvider::class,
        AuthServiceProvider::class,
        JWTAuthServiceProvider::class,
        EventServiceProvider::class,
        ApiSoftDeletesServiceProvider::class,
        LuminaryModelServiceProvider::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        collect($this->providers)->each(function ($provider) {
            $this->app->register($provider);
        });
    }
}
