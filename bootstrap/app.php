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

$app->withEloquent();

// Set the application start time
$app->make('config')->set('app.start', $start);

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

//$app->middleware([]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

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
    \Luminary\Services\ApiLoader\Loaders\EntityLoader::class,
    \Luminary\Services\ApiLoader\Loaders\ResourceLoader::class,
    \Luminary\Services\ApiLoader\Loaders\ServiceLoader::class
 ]);

 $api->registerConsole();
 $api->registerModelFactories();
 $api->registerMiddleware();
 $api->registerMigrations();
 $api->registerProviders();
 $api->registerRoutes();
 $api->registerRouteMiddleware();
 $api->registerSeeders();

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

 return $app;
