<?php

namespace Luminary\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Class RouteList
 *
 * @credit: https://github.com/appzcoder/lumen-route-list
 * @package Luminary\Console\Commands
 */
class RouteList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:list';

    /**
     * List of column headers for the
     * route list table
     *
     * @var array
     */
    protected $headers = [
        'Verb',
        'Path',
        'NamedRoute',
        'Controller',
        'Action',
        'Middleware'
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->table($this->headers(), $this->rows());
    }

    /**
     * Get the controller name from the route action
     *
     * @param array $routeAction
     * @return mixed|string
     */
    protected function controller(array $routeAction)
    {
        $uses = array_get($routeAction, 'uses');
        return $uses ? current(explode("@", $uses)) : 'None';
    }

    /**
     * Get the controller method name from the route action
     *
     * @param array $routeAction
     * @return string
     */
    protected function controllerMethod(array $routeAction)
    {
        $uses = array_get($routeAction, 'uses');

        if ($uses) {
            return (($pos = strpos($uses, "@")) !== false)
                ? substr($uses, $pos + 1)
                : "¯\\_(ツ)_/¯";
        }

        return 'Closure';
    }

    /**
     * Get the table headers
     *
     * @return array
     */
    protected function headers()
    {
        return $this->headers;
    }

    /**
     * Get the route middleware from the route action
     *
     * @param array $routeAction
     * @return string
     */
    protected function middleware(array $routeAction)
    {
        $middleware = array_get($routeAction, 'middleware');

        if (is_array($middleware)) {
            return implode(', ', $middleware);
        }

        return $middleware;
    }

    /**
     * Get the named route from the route action
     *
     * @param array $routeAction
     * @return string
     */
    protected function namedRoute(array $routeAction)
    {
        return array_get($routeAction, 'as');
    }

    /**
     * Get a collection of application routes
     *
     * @return Collection
     */
    protected function routes() :Collection
    {
        $routes = app()->router->getRoutes();
        return collect($routes);
    }

    /**
     * Generate the table rows
     *
     * @return array
     */
    protected function rows() :array
    {
        return $this->routes()->map(
            function ($route) {
                $action = array_get($route, 'action');
                $method = array_get($route, 'method');
                $uri = array_get($route, 'uri');

                return [
                    'verb' => $method,
                    'path' => $uri,
                    'namedRoute' => $this->namedRoute($action),
                    'controller' => $this->controller($action),
                    'action' => $this->controllerMethod($action),
                    'middleware' => $this->middleware($action),
                ];
            }
        )->all();
    }
}
