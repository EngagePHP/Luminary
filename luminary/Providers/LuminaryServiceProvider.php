<?php

namespace Luminary\Providers;

use Illuminate\Support\ServiceProvider;
use Luminary\Services\Filesystem\FilesystemServiceProvider;
use Luminary\Services\ApiLoader\ApiLoaderServiceProvider;

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
