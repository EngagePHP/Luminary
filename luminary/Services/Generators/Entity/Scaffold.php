<?php

namespace Luminary\Services\Generators\Entity;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Generators\Creators\Database\Factory;
use Luminary\Services\Generators\Creators\Database\Migration;
use Luminary\Services\Generators\Creators\Database\Seeder;
use Luminary\Services\Generators\Creators\Database\Structure as DatabaseStructure;
use Luminary\Services\Generators\Creators\Events\Structure as EventStructure;
use Luminary\Services\Generators\Creators\Middleware\Middleware;
use Luminary\Services\Generators\Creators\Middleware\Structure as MiddlewareStructure;
use Luminary\Services\Generators\Creators\Models\Model;
use Luminary\Services\Generators\Creators\Models\Structure as ModelStructure;
use Luminary\Services\Generators\Creators\Repositories\Structure as RepositoryStructure;
use Luminary\Services\Generators\Creators\Repositories\Repository;
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
        static::repository(...$args);
        static::tests(...$args);
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
        $lname = strtolower($name);
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
}