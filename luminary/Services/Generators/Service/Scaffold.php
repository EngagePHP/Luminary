<?php

namespace Luminary\Services\Generators\Service;

use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Generators\Creators\Console\Command;
use Luminary\Services\Generators\Creators\Console\Kernel;
use Luminary\Services\Generators\Creators\Console\Structure as CommandStructure;
use Luminary\Services\Generators\Creators\Providers\ServiceProvider;
use Luminary\Services\Generators\Creators\Tests\ServiceTest;

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

        static::console(...$args);
        static::provider(...$args);
        static::tests(...$args);
    }

    /**
     * Scaffold the service Console folder
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function console(string $name, string $path) :void
    {
        $slug = str_slug($name);

        CommandStructure::create($path);
        Kernel::create('Kernel', $path.'/Console', ['commands' => 'Commands\\'.$name.'Command::class']);
        Command::create($name.'Command', $path.'/Console/Commands', ['name' => $slug, 'signature' => $slug]);
    }

    /**
     * Scaffold the service provider
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    protected static function provider(string $name, string $path) :void
    {
        ServiceProvider::create($name.'ServiceProvider', $path);
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

        ServiceTest::create($singular.'Service', $directory)->link($target);
    }
}
