<?php

namespace Luminary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class LuminaryModelServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider
     */
    public function boot()
    {
        $this->app['events']->listen('eloquent.booting: *', function($event, $data) {
            $model = array_first($data);
            $this->addExpiredObservables($model);
            $this->addArchivedObservables($model);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Nothing to see here
    }

    /**
     * Add the expired observable events
     *
     * @param Model $model
     */
    protected function addExpiredObservables(Model $model)
    {
        if(method_exists($model, 'getExpiredAtColumn')) {
            $model->setExpiredObservables();
        }
    }

    /**
     * Add the archived observable events
     *
     * @param Model $model
     */
    protected function addArchivedObservables(Model $model)
    {
        if(method_exists($model, 'getArchivedAtColumn')) {
            $model->setArchivedObservables();
        }
    }
}
