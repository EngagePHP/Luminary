<?php

namespace Luminary\Services\Generators\Resource;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Generators\Creators\Authorizers\Authorizer;
use Luminary\Services\Generators\Creators\Controllers\Controller;
use Luminary\Services\Generators\Creators\Controllers\RelatedController;
use Luminary\Services\Generators\Creators\Controllers\RelationshipController;
use Luminary\Services\Generators\Creators\Controllers\Structure as ControllerStructure;
use Luminary\Services\Generators\Creators\Requests\Request;
use Luminary\Services\Generators\Creators\Requests\Structure as RequestStructure;
use Luminary\Services\Generators\Creators\RouteMiddleware\Registry;
use Luminary\Services\Generators\Creators\RouteMiddleware\Structure as RouteMiddlewareStructure;
use Luminary\Services\Generators\Creators\Routes\Routes;
use Luminary\Services\Generators\Creators\Sanitizers\Sanitizer;
use Luminary\Services\Generators\Creators\Tests\ResourceTest;
use Luminary\Services\Generators\Creators\Validators\Validator;
use Luminary\Services\Generators\Creators\Validators\Structure as ValidatorStructure;

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
        static::controllers(...$args);
        static::route(...$args);
        static::tests(...$args);
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
        $requestsPath = $path.'/Requests';
        $name = studly_case(str_singular($name));
        $relative_path = str_replace(app_path() . '/', 'Api/', $path);
        $relative_path = str_replace('Resources', 'Entities', $relative_path);
        $model = str_replace('/', '\\', $relative_path . '/Models/' . $name);

        RequestStructure::create($path);
        Validator::create('Store', $requestsPath . '/Validators');
        Validator::create('Update', $requestsPath . '/Validators');
        Authorizer::create('Auth', $requestsPath, $model);
        Sanitizer::create('Sanitizer', $requestsPath);
    }

    /**
     * Scaffold the resource controller folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function controllers(string $name, string $path) :void
    {
        $name = studly_case(str_singular($name));
        $relative_path = str_replace(app_path() . '/', 'Api/', $path);
        $relative_path = str_replace('Resources', 'Entities', $relative_path);
        $namespace = str_replace('/', '\\', $relative_path . '/Repositories/' . $name);

        ControllerStructure::create($path);

        // Create default controller
        Controller::create($name.'Controller', $path . '/Controllers', [
            'repositoryBasename' => $name . 'Repository',
            'repositoryNamespace' => $namespace . 'Repository'
        ]);

        // Create related controller
        RelatedController::create($name.'RelatedController', $path . '/Controllers', [
            'repositoryBasename' => $name . 'RelatedRepository',
            'repositoryNamespace' => $namespace . 'RelatedRepository'
        ]);

        // Create relationship controller
        RelationshipController::create($name.'RelationshipController', $path . '/Controllers', [
            'repositoryBasename' => $name . 'RelationshipRepository',
            'repositoryNamespace' => $namespace . 'RelationshipRepository'
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
        $slug = str_slug(str_plural($name));
        $routeName = snake_case(str_replace(['-', ' '], '.', $name));
        $name = studly_case(str_singular($name));
        $relative_path = str_replace(app_path().'/', 'Api/', $path);
        $namespace = str_replace('/', '\\', $relative_path.'/Controllers');

        Routes::create('routes', $path, [
            'controller' => $name.'Controller',
            'relatedController' => $name.'RelatedController',
            'relationshipController' => $name.'RelationshipController',
            'namespace' => $namespace,
            'slug' => $slug,
            'routeName' => $routeName
        ]);
    }

    /**
     * Create the entity tests folder and initial test
     *
     * @return void
     */
    protected static function tests(string $name, string $path) :void
    {
        $target = $path.'/Tests';
        $directory = app_path('tests/'.$name);
        $singular = str_singular($name);
        $slug = str_slug(str_plural($name));

        ResourceTest::create($singular.'Resource', $directory, ['slug' => $slug])->link($target);
    }
}
