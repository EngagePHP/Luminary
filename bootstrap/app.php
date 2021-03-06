<?php

$start = microtime(true);

require_once __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Luminary\Application(
    realpath(__DIR__.'/../')
);

$app->configure('view');
$app->configure('permission');

$app->withEloquent();
$app->alias('cache', \Illuminate\Cache\CacheManager::class);

// Set the application start time
config(['app' => ['start' => $start]]);

/*
|--------------------------------------------------------------------------
| Set Log Handling
|--------------------------------------------------------------------------
|
| Now we will modify the log handling to stderr for docker container output
|
*/

$app->configureMonologUsing(function (\Monolog\Logger $monolog) {
    $monolog->pushHandler(new \Monolog\Handler\ErrorLogHandler());

    return $monolog;
});

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Luminary\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Luminary\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Barryvdh\Cors\HandleCors::class,
]);

$app->routeMiddleware([
    'request.headers' => \Luminary\Services\ApiRequest\Middleware\RequestHeaders::class,
    'request.middleware' => Luminary\Services\ApiRequest\Middleware\RequestRouteMiddleware::class,
    'query' => \Luminary\Services\ApiQuery\QueryMiddleware::class,
    'response' => Luminary\Services\ApiResponse\ResponseRouteMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Load Configurations
|--------------------------------------------------------------------------
|
| Here we will can load registered provider configs for the application
|
*/
$app->configure('luminary');
$app->configure('cors');

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Luminary\Providers\LuminaryServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);
$app->register(Spatie\Permission\PermissionServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load A Custom Bootstrap file
|--------------------------------------------------------------------------
|
| Next we will include a custom bootstrap.php file if it exists in the
| api directory
|
*/

$boostrap = realpath(__DIR__ . '/../api/bootstrap.php');

if (file_exists($boostrap)) {
    include $boostrap;
}

/*
|--------------------------------------------------------------------------
| Load the Api
|--------------------------------------------------------------------------
|
| Now we will load the API registry class for autoloading the API folder
| structure.
|
*/

$api = $app->loadApi([
    \Luminary\Services\ApiLoader\Loaders\ConfigLoader::class,
    \Luminary\Services\ApiLoader\Loaders\EntityLoader::class,
    \Luminary\Services\ApiLoader\Loaders\ResourceLoader::class,
    \Luminary\Services\ApiLoader\Loaders\ServiceLoader::class
]);

$api->registerAuthorizers();
$api->registerConfigs();
$api->registerConsole();
$api->registerEvents();
$api->registerModelFactories();
$api->registerMiddleware();
$api->registerMigrations();
$api->registerMorphMaps();
$api->registerPolicies();
$api->registerProviders();
$api->registerRoutes(['jwt.auth', 'request.headers', 'request.middleware','query','response']);
$api->registerRouteMiddleware();
$api->registerSanitizers();
$api->registerSeeders();
$api->registerValidators();

$app->register(\Luminary\Services\Routing\ServiceProvider::class);

return $app;
