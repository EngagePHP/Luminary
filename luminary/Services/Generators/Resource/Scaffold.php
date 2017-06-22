<?php

namespace Luminary\Services\Generators\Resource;

use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Generators\Creators\Controllers\Controller;
use Luminary\Services\Generators\Creators\Controllers\Structure as ControllerStructure;
use Luminary\Services\Generators\Creators\Requests\Request;
use Luminary\Services\Generators\Creators\Requests\Structure as RequestStructure;
use Luminary\Services\Generators\Creators\RouteMiddleware\Registry;
use Luminary\Services\Generators\Creators\RouteMiddleware\Structure as RouteMiddlewareStructure;
use Luminary\Services\Generators\Creators\Routes\Routes;

class Scaffold implements CreatorInterface
{
    /**
     * Scaffold a new folder structure
     *
     * @param string $name
     * @param string $path
     * @return mixed
     */
    public static function create(string $name, string $path)
    {
        $args = func_get_args();

        static::routeMiddleware(...$args);
        static::request(...$args);
        static::controller(...$args);
        static::route(...$args);
    }

    /**
     * Scaffold the resource route middleware folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function routeMiddleware(string $name, string $path) :void
    {
        RouteMiddlewareStructure::create($path);
        Registry::create('registry', $path . '/Middleware');
    }

    /**
     * Scaffold the resource request folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function request(string $name, string $path) :void
    {
        $name = studly_case(str_singular($name));
        $requestsPath = $path.'/Requests';

        RequestStructure::create($path);
        Request::create($name.'IndexRequest', $requestsPath);
        Request::create($name.'ShowRequest', $requestsPath);
        Request::create($name.'StoreRequest', $requestsPath);
        Request::create($name.'UpdateRequest', $requestsPath);
        Request::create($name.'DestroyRequest', $requestsPath);
    }

    /**
     * Scaffold the resource controller folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function controller(string $name, string $path) :void
    {
        $name = studly_case(str_singular($name));
        $relative_path = str_replace(app_path().'/', 'Api/', $path);
        $namespace = str_replace('/', '\\', $relative_path.'/Requests');

        ControllerStructure::create($path);
        Controller::create($name.'Controller', $path . '/Controllers', [
            'requestBasename' => $name,
            'requestNamespace' => $namespace
        ]);
    }

    /**
     * Scaffold the resource route file
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function route(string $name, string $path) :void
    {
        $name = studly_case(str_singular($name));
        $slug = str_slug(str_plural($name));
        $relative_path = str_replace(app_path().'/', 'Api/', $path);
        $namespace = str_replace('/', '\\', $relative_path.'/Controllers');

        Routes::create('routes', $path, [
            'controller' => $name.'Controller',
            'namespace' => $namespace,
            'slug' => $slug
        ]);
    }
}
