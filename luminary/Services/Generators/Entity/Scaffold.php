<?php

namespace Luminary\Services\Generators\Entity;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Generators\Creators\Database\Factory;
use Luminary\Services\Generators\Creators\Database\Migration;
use Luminary\Services\Generators\Creators\Database\Seeder;
use Luminary\Services\Generators\Creators\Database\Structure as DatabaseStructure;
use Luminary\Services\Generators\Creators\Events\Registrar as EventRegistrar;
use Luminary\Services\Generators\Creators\Events\Structure as EventStructure;
use Luminary\Services\Generators\Creators\Middleware\Middleware;
use Luminary\Services\Generators\Creators\Middleware\Structure as MiddlewareStructure;
use Luminary\Services\Generators\Creators\Models\Model;
use Luminary\Services\Generators\Creators\Models\Structure as ModelStructure;
use Luminary\Services\Generators\Creators\Policies\Migration as PolicyMigration;
use Luminary\Services\Generators\Creators\Policies\Policy;
use Luminary\Services\Generators\Creators\Policies\PolicyRegistrar;
use Luminary\Services\Generators\Creators\Policies\Structure as PolicyStructure;
use Luminary\Services\Generators\Creators\Repositories\RelatedRepository;
use Luminary\Services\Generators\Creators\Repositories\RelationshipRepository;
use Luminary\Services\Generators\Creators\Repositories\Structure as RepositoryStructure;
use Luminary\Services\Generators\Creators\Repositories\Repository;
use Luminary\Services\Generators\Creators\RouteMiddleware\Registry;
use Luminary\Services\Generators\Creators\RouteMiddleware\Structure as RouteMiddlewareStructure;
use Luminary\Services\Generators\Creators\Requests\Structure as RequestStructure;
use Luminary\Services\Generators\Creators\Validators\Validator;
use Luminary\Services\Generators\Creators\Sanitizers\Sanitizer;
use Luminary\Services\Generators\Creators\Authorizers\Authorizer;
use Luminary\Services\Generators\Creators\Tests\ModelTest;

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

        static::database(...$args);
        static::event(...$args);
        static::middleware(...$args);
        static::policy(...$args);
        static::repository(...$args);
        //static::tests(...$args);

        if(config('luminary.dynamic_routing') !== false) {
            static::routeMiddleware(...$args);
            static::request(...$args);
        }
    }

    /**
     * Scaffold the entity database folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function database(string $name, string $path) :void
    {
        $lname = strtolower(snake_case($name));
        $singular = str_singular($name);

        // DB
        DatabaseStructure::create($path);
        Migration::create('create_' . $lname . '_table', $path . '/Database/Migrations', $lname, true, true);
        Seeder::create($singular . 'Seeder', $path . '/Database/Seeds');

        // Model
        ModelStructure::create($path);
        $model = Model::create($singular, $path.'/Models', ['table' => $lname]);
        Factory::create($singular . 'Factory', $path . '/Database/Factories', [
            'modelNamespace' => $model->rootNamespace().'\\'.$singular,
            'modelName' => $singular
        ]);
    }

    /**
     * Scaffold the entity events folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function event(string $name, string $path) :void
    {
        EventStructure::create($path);
        EventRegistrar::create('EventRegistrar', $path.'/Events');
        Storage::gitKeep($path.'/Events/Jobs');
        Storage::gitKeep($path.'/Events/Messages');
    }

    /**
     * Scaffold the entity middleware folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function middleware(string $name, string $path) :void
    {
        $singular = str_singular($name);

        MiddlewareStructure::create($path);
        Middleware::create($singular.'Middleware', $path.'/Middleware');
    }

    /**
     * Scaffold the entity repositories folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function repository(string $name, string $path) :void
    {
        $singular = str_singular($name);
        $relative_path = str_replace(app_path().'/', 'Api/', $path);
        $model = str_replace('/', '\\', $relative_path.'/Models/'.$singular);

        RepositoryStructure::create($path);
        Repository::create($singular.'Repository', $path.'/Repositories', ['model' => $model]);
        RelatedRepository::create($singular.'RelatedRepository', $path.'/Repositories', ['model' => $model]);
        RelationshipRepository::create($singular.'RelationshipRepository', $path.'/Repositories', ['model' => $model]);
    }

    /**
     * Scaffold the policy folder
     *
     * @param string $name
     * @param string $path
     */
    protected static function policy(string $name, string $path)
    {
        $lname = strtolower(snake_case($name));
        $singular = str_singular($name);
        $relative_path = str_replace(app_path().'/', 'Api/', $path);
        $model = str_replace('/', '\\', $relative_path.'/Models/'.$singular);

        PolicyStructure::create($path);

        Policy::create($singular . 'Policy', $path . '/Policies', [
            'model' => $model,
        ]);

        PolicyMigration::create('create_' . $lname . '_permissions', $path . '/Database/Migrations', $model);

        PolicyRegistrar::create('PolicyRegistrar', $path . '/Policies', [
            'model' => $model,
            'policy' => $singular . 'Policy'
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

        ModelTest::create($singular.'Model', $directory)->link($target);
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
        Registry::create('registry', $path . '/Http/Middleware');
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
        $requestsPath = $path.'/Http/Requests';
        $name = studly_case(str_singular($name));
        $relative_path = str_replace(app_path() . '/', 'Api/', $path);
        $model = str_replace('/', '\\', $relative_path . '/Models/' . $name);

        RequestStructure::create($path);
        Validator::create('Store', $requestsPath . '/Validators');
        Validator::create('Update', $requestsPath . '/Validators');
        Authorizer::create('Auth', $requestsPath, $model);
        Sanitizer::create('Sanitizer', $requestsPath);
    }
}
