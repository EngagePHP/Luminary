<?php

namespace Luminary\Services\ApiLoader;

use Faker\Generator;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use Luminary\Application;
use Luminary\Contracts\Console\Kernel;
use Luminary\Services\ApiLoader\Helpers\Cache;
use Luminary\Services\ApiLoader\Helpers\Directory;
use Luminary\Services\ApiLoader\Registry\Registrar;
use Luminary\Services\ApiLoader\Registry\Registry;
use Luminary\Services\Filesystem\App\Storage;

class ApiLoader
{
    /**
     * The Api path from root
     *
     * @var string
     */
    public $basePath = 'api';

    /**
     * The Registrar Instance
     *
     * @var \Luminary\Services\ApiLoader\Registry\Registrar
     */
    protected $registrar;

    /**
     * The Registry Instance
     *
     * @var \Luminary\Services\ApiLoader\Registry\Registry
     */
    protected $registry;


    /**
     * The Luminary Application Instance
     *
     * @var Application
     */
    protected $app;

    /**
     * ApiLoader constructor.
     * @param Registrar $registrar
     * @param Registry $registry
     * @param Application $app
     */
    public function __construct(Registrar $registrar, Registry $registry, Application $app)
    {
        $this->registrar = $registrar;
        $this->registry = $registry;
        $this->app = $app;
    }

    /**
     * Dynamically load the ApiLoaders
     *
     * @param array $loaders
     * @return \Luminary\Services\ApiLoader\ApiLoader
     */
    public function load(array $loaders) :ApiLoader
    {
        if (! $this->loadCached()) {
            foreach ($loaders as $loader) {
                $loader = new $loader($this->registrar);
                $path = $this->basePath($loader->path());

                // Run loader only if directory exists
                if (! Storage::isDirectory($path)) {
                    continue;
                }

                $loader->load($path);
            }
        }

        return $this;
    }

    /**
     * Load the API Loader cache
     * if exists
     *
     * @return bool
     */
    public function loadCached()
    {
        if (! Cache::exists()) {
            return false;
        }

        $cached = Cache::get();
        $this->registry->fill($cached);

        return true;
    }

    /**
     * Register a Console Kernels and Commands
     *
     * @return void
     */
    public function registerConfigs() :void
    {
        $this->registry('configs')->each(
            function ($config) {
                $ext = pathinfo($config, PATHINFO_EXTENSION);
                $name = basename($config, '.' . $ext);
                $config = (array) include $config;

                config([$name => $config]);
            }
        );
    }

    /**
     * Register a Console Kernels and Commands
     *
     * @return void
     */
    public function registerConsole() :void
    {
        $this->registerConsoleKernels();
        $this->registerCommands(
            $this->registry('commands')->toArray()
        );
    }

    /**
     * Register an array of Console Kernels
     *
     * @return void
     */
    public function registerConsoleKernels() :void
    {
        $schedule = $this->app[Schedule::class];

        $this->registry('consoleKernels')->each(
            function ($kernel) use ($schedule) {
                $this->registerConsoleKernel(new $kernel, $schedule);
            }
        );
    }

    /**
     * Register a Console Kernel
     *
     * @param Kernel $kernel
     * @param Schedule $schedule
     * @return void
     */
    public function registerConsoleKernel(Kernel $kernel, Schedule $schedule) :void
    {
        $this->registerCommands($kernel->commands());
        $kernel->schedule($schedule);
    }

    /**
     * Register Artisan Commands
     *
     * @param array $commands
     * @return void
     */
    public function registerCommands(array $commands) :void
    {
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }

    /**
     * Register The Api Model Factories
     *
     * @return void
     */
    public function registerModelFactories() :void
    {
        $paths = $this->registry('modelFactories')->toArray();

        $factory = app(ModelFactory::class);

        foreach ($paths as $path) {
            $factory->load($path);
        }
    }

    /**
     * Register The Api Application Middleware
     *
     * @return void
     */
    public function registerMiddleware() :void
    {
        $this->app->middleware(
            $this->registry('middleware')->toArray()
        );
    }

    /**
     * Register API Migrations
     *
     * @return void
     */
    public function registerMigrations() :void
    {
        $this->app->afterResolving('migrator', function ($migrator) {
            $this->registry('migrations')->each(
                function ($migration) use ($migrator) {
                    $migrator->path($migration);
                }
            );
        });
    }

    /**
     * Register API Service Providers
     *
     * @return void
     */
    public function registerProviders() :void
    {
        $this->registry('providers')->each(
            function ($provider) {
                $this->app->register($provider);
            }
        );
    }

    /**
     * Register API Route Middleware
     *
     * @return void
     */
    public function registerRouteMiddleware() :void
    {
        $this->app->routeMiddleware(
            $this->registry('routeMiddleware')->toArray()
        );
    }

    /**
     * Register Api Routes
     *
     * @return void
     */
    public function registerRoutes() :void
    {
        $this->registry('routes')->each(
            function ($route) {
                $this->loadRoutesFrom($route);
            }
        );
    }

    /**
     * Register the seeders and bind
     * the DatabaseSeeder
     *
     * @return void
     */
    public function registerSeeders() :void
    {
        $this->app->bind('DatabaseSeeder', function ($app) {
            $seeders = $this->registry('seeders')->toArray();
            return new Database\DatabaseSeeder($seeders);
        });
    }

    /**
     * Get the registry or an item from
     * the registry by key
     *
     * @param null $key
     * @return \Illuminate\Support\Collection|Registry
     */
    public function registry($key = null)
    {
        return $key ? $this->registry->{$key} : $this->registry;
    }

    /**
     * Return the full path by config key
     *
     * @param string $path
     * @return string
     */
    protected function basePath(string $path) :string
    {
        $basePath = base_path($this->basePath);

        return Directory::make($basePath)->path($path);
    }

    /**
     * Override the default routes loader
     *
     * @param string $path
     */
    protected function loadRoutesFrom($path)
    {
        $app = $this->app;
        $router = $app->router;

        require $path;
    }
}
