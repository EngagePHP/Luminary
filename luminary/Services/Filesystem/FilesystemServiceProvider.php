<?php

namespace Luminary\Services\Filesystem;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider as LaravelFilesystemServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('filesystems');
        $this->app->register(LaravelFilesystemServiceProvider::class);
    }
}
