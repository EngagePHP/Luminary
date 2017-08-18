<?php

namespace Luminary;

use Laravel\Lumen\Application as LaravelApplication;
use Luminary\Console\ConsoleServiceProvider;
use Luminary\Services\ApiLoader\ApiLoader;
use Luminary\Services\ApiLoader\Registry\Registrar;
use Luminary\Services\ApiLoader\Registry\Registry;

class Application extends LaravelApplication
{
    public $luminaryAvailableBindings = [
        'api.loader' => 'registerApiLoaderBindings',
    ];

    /**
     * Create a new Lumen application instance.
     *
     * @param  string|null  $basePath
     */
    public function __construct($basePath = null)
    {
        $this->registerLuminaryAvailableBindings();

        parent::__construct($basePath);
    }

    /**
     * Dynamically load the ApiLoaders
     *
     * @return \Luminary\Services\ApiLoader\ApiLoader
     */
    public function loadApi(array $loaders) :ApiLoader
    {
        $loader = $this['api.loader'];

        $loader->load($loaders);

        return $loader;
    }

    /**
     * Prepare the application to execute a console command.
     *
     * @param  bool  $aliases
     * @return void
     */
    public function prepareForConsoleCommand($aliases = true)
    {
        parent::prepareForConsoleCommand($aliases);

        $this->register(ConsoleServiceProvider::class);
    }

    /**
     * Register the core container aliases.
     *
     * @return void
     */
    protected function registerContainerAliases()
    {
        parent::registerContainerAliases();

        $this->aliases = array_merge(
            $this->aliases,
            [
                // Add additional aliases here
            ]
        );
    }

    protected function registerApiLoaderBindings()
    {
        $this->singleton('api.loader', function () {
            $registry = new Registry;
            $registrar = new Registrar($registry);

            return new Services\ApiLoader\ApiLoader($registrar, $registry, $this);
        });
    }

    protected function registerLuminaryAvailableBindings()
    {
        $this->availableBindings = array_merge(
            $this->availableBindings,
            $this->luminaryAvailableBindings
        );
    }

    public function group(array $attributes, \Closure $callback)
    {
        parent::group($attributes, $callback);
    }
}
