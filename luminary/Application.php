<?php

namespace Luminary;

use Laravel\Lumen\Application as LaravelApplication;
use Luminary\Concerns\RoutesRequests;
use Luminary\Console\ConsoleServiceProvider;
use Luminary\Services\ApiLoader\ApiLoader;
use Luminary\Services\ApiLoader\Registry\Registrar;
use Luminary\Services\ApiLoader\Registry\Registry;

class Application extends LaravelApplication
{
    use RoutesRequests;

    /**
     * Laravel Lumen Version
     */
    const VERSION = 5.6;

    /**
     * Additional bindings for luminary application
     *
     * @var array
     */
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
        $this->withFacades($aliases);

        $this->make('cache');
        $this->make('queue');

        $this->configure('database');

        $this->register('Illuminate\Database\MigrationServiceProvider');
        $this->register('Laravel\Lumen\Console\ConsoleServiceProvider');

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

    /**
     * Register the ApiLoader with Lumen
     *
     * @return void
     */
    protected function registerApiLoaderBindings()
    {
        $this->singleton('api.loader', function () {
            $registry = new Registry;
            $registrar = new Registrar($registry);

            return new Services\ApiLoader\ApiLoader($registrar, $registry, $this);
        });
    }

    /**
     * Add luminary bindings to the default Lumen bindings
     *
     * @return void
     */
    protected function registerLuminaryAvailableBindings()
    {
        $this->availableBindings = array_merge(
            $this->availableBindings,
            $this->luminaryAvailableBindings
        );
    }
}
