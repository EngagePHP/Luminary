<?php

namespace Luminary\Services\ApiLoader\Registry;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\ApiLoader\Helpers\Directory;

class Registrar
{
    /**
     * The Registry Instance
     *
     * @var \Luminary\Services\ApiLoader\Registry\Registry
     */
    private $registry;

    /**
     * The Directory Instance
     *
     * @var \Luminary\Services\ApiLoader\Helpers\Directory
     */
    protected $directory;

    /**
     * AbstractApiLoader constructor.
     *
     * @param \Luminary\Services\ApiLoader\Registry\Registry $registry
     * @param \Luminary\Services\ApiLoader\Helpers\Directory $directory
     */
    public function __construct(Registry $registry, Directory $directory = null)
    {
        $this->registry = $registry;
        $this->directory = $directory;
    }

    /**
     * Get the Directory instance
     *
     * @return \Luminary\Services\ApiLoader\Helpers\Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * Set the Directory instance
     *
     * @param \Luminary\Services\ApiLoader\Helpers\Directory $directory
     */
    public function setDirectory(Directory $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Get the full directory path
     * from a relative path
     *
     * @param $path
     * @return mixed
     */
    public function path($path = '')
    {
        return $this->directory->path($path);
    }

    /**
     * Is path a directory?
     *
     * @param $path
     * @return mixed
     */
    public function isDirectory($path)
    {
        return Storage::isDirectory($path);
    }

    /**
     * Is path a file?
     *
     * @param $path
     * @return mixed
     */
    public function isFile($path)
    {
        return Storage::isFile($path);
    }

    /**
     * Get the path Basename
     *
     * @return string
     */
    public function baseName()
    {
        return basename($this->path());
    }

    /**
     * Register a resource authorizer
     *
     * @param string $path
     * @return void
     */
    public function registerAuthorizer($path)
    {
        $resource = function($path) {
            $basename = basename($path);
            $split = snake_case($basename);
            return str_slug($split);
        };

        $path = $this->path($path);

        if (! $this->isFile($path)) {
            return;
        }

        $dir = dirname($path);
        $basename = basename($path, ".php");
        $authorizer = $this->directory->make($dir)->class($basename);

        $this->registry->authorizers = [[$resource($this->path()), $authorizer]];
    }

    /**
     * Register a console kernel or
     * directory of command classes
     *
     * @param string $path
     * @return void
     */
    public function registerConfigs(string $path = '') :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $files = array_filter(
            $this->directory->make($path)->files(),
            function ($file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                return strtolower($ext) == 'php';
            }
        );

        $this->registry->configs = $files;
    }

    /**
     * Register a console kernel or
     * directory of command classes
     *
     * @param string $path
     * @return void
     */
    public function registerConsole(string $path) :void
    {
        $kernel = $this->path($path).'/Kernel.php';

        if ($this->isFile($kernel)) {
            $this->registerConsoleKernel($path.'/Kernel.php');
        } else {
            $this->registerCommands($path.'/Commands');
        }
    }

    /**
     * Register Application Console Kernels
     *
     * @param string $kernel
     * @return void
     */
    public function registerConsoleKernel(string $kernel) :void
    {
        $kernel = $this->path($kernel);

        $dir = dirname($kernel);
        $basename = basename($kernel, ".php");
        $kernel = $this->directory->make($dir)->class($basename);

        if (! class_exists($kernel)) {
            return;
        }

        $this->registry->consoleKernels = (array) $kernel;
    }

    /**
     * Register Artisan commands by path
     *
     * @param string $path
     * @return void
     */
    public function registerCommands(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->commands = $this->directory->make($path)->classes();
    }

    /**
     * Register Application Events
     *
     * @param string $eventRegistrar
     * @return void
     */
    public function registerEvents(string $eventRegistrar) :void
    {
        $registrar = $this->path($eventRegistrar);

        if (! file_exists($registrar)) {
            return;
        }

        $dir = dirname($registrar);
        $basename = basename($registrar, ".php");
        $registrar = $this->directory->make($dir)->class($basename);

        if (! class_exists($registrar)) {
            return;
        }

        // Instantiate the registrar class
        $registrar = new $registrar;

        $this->registerEventListeners($registrar->listeners());
        $this->registerEventSubscribers($registrar->subscribers());
        $this->registerEventMaps($registrar->mapped());
    }

    /**
     * Register the application event listeners
     *
     * @param array $listeners
     */
    public function registerEventListeners(array $listeners) :void
    {
        $this->registry->eventListeners = array_merge_recursive(
            $this->registry->eventListeners->all(),
            $listeners
        );
    }

    /**
     * Register the application event maps
     *
     * @param array $map
     */
    public function registerEventMaps(array $map) :void
    {
        $this->registry->eventMaps = array_merge_recursive(
            $this->registry->eventMaps->all(),
            $map
        );
    }

    /**
     * Register the application event subscribers
     *
     * @param array $subscribers
     */
    public function registerEventSubscribers(array $subscribers) :void
    {
        $subscribers = array_diff(
            $subscribers,
            $this->registry->eventSubscribers->all()
        );

        $this->registry->eventSubscribers = array_unique($subscribers);
    }

    /**
     * Register a model factory path
     *
     * @param string $path
     * @return void
     */
    public function registerModelFactories(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->modelFactories = (array) $path;
    }

    /**
     * Register Middleware
     *
     * @param string $path
     * @return void
     */
    public function registerMiddleware(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->middleware = $this->directory->make($path)->classes();
    }

    /**
     * Register migrations
     *
     * @param string $path
     * @return void
     */
    public function registerMigrations($path) :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->migrations = (array) $path;
    }

    /**
     * Regist model morph maps
     *
     * @param $path
     */
    public function registerMorphMap($path) :void
    {
        $path = $this->path($path);

        if(! $this->isDirectory($path)) {
            return;
        }

        $basename = $this->baseName($path);
        $singular = str_singular($basename);
        $class = $this->getDirectory()->make($path)->class($this->path($singular));

        if(class_exists($class)) {
            $name = str_slug(snake_case($basename));
            $this->registry->morphMaps = (array) [$name => $class];
        }
    }

    /**
     * Register a provider file
     *
     * @param string $policyRegistrar
     */
    public function registerPolicies(string $policyRegistrar) :void
    {
        $registrar = $this->path($policyRegistrar);

        if (! file_exists($registrar)) {
            return;
        }

        $dir = dirname($registrar);
        $basename = basename($registrar, ".php");
        $registrar = $this->directory->make($dir)->class($basename);

        if (! class_exists($registrar)) {
            return;
        }

        // Instantiate the registrar class
        $registrarInstance = new $registrar;

        $this->registry->policyRegistrars = (array) $registrar;
        $this->registry->policies = $registrarInstance->policies();
    }

    /**
     * Register a provider file
     *
     * @param string $provider
     * @return void
     */
    public function registerProvider(string $provider) :void
    {
        $provider = $this->directory->class($provider);

        if (! class_exists($provider)) {
            return;
        }

        $this->registry->providers = (array) $provider;
    }

    /**
     * Register a routes file
     *
     * @param string $path
     * @return void
     */
    public function registerRoutes(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isFile($path)) {
            return;
        }

        $this->registry->routes = (array) $path;
    }

    /**
     * Register a custom routes file
     *
     * @param string $path
     * @return void
     */
    public function registerCustomRoutes(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isFile($path)) {
            return;
        }

        $this->registry->customRoutes = (array) $path;
    }

    /**
     * Register Route Middleware
     *
     * @param string $path
     * @return void
     */
    public function registerRouteMiddleware(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isFile($path)) {
            return;
        }

        $this->registry->routeMiddleware = (array) $path;
    }

    /**
     * Register a resource authorizer
     *
     * @param string $path
     * @return void
     */
    public function registerSanitizer($path)
    {
        $resource = strtolower(basename($this->path()));
        $path = $this->path($path);

        if (! $this->isFile($path)) {
            return;
        }

        $dir = dirname($path);
        $basename = basename($path, ".php");
        $sanitizer = $this->directory->make($dir)->class($basename);

        $this->registry->sanitizers = [[$resource, $sanitizer]];
    }

    /**
     * Register database seeders
     *
     * @param string $path
     * @return void
     */
    public function registerSeeders(string $path) :void
    {
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->seeders = $this->directory->make($path)->classes();
    }

    /**
     * Register database seeders
     *
     * @param string $path
     * @return void
     */
    public function registerValidators(string $path) :void
    {
        $resource = strtolower(basename($this->path()));
        $path = $this->path($path);

        if (! $this->isDirectory($path)) {
            return;
        }

        $classes = $this->directory->make($path)->classes();

        $this->registry->validators = array_map(function ($class) use ($resource) {
            $type = strtolower(substr(strrchr($class, '\\'), 1));
            return [$resource, $type, $class];
        }, $classes);
    }

    /**
     * Register a view folder
     *
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public function registerViews(string $namespace, string $path) :void
    {
        if (! $this->isDirectory($path)) {
            return;
        }

        $this->registry->views = [$namespace => $path];
    }
}
