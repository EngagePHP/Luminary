<?php

namespace Luminary\Services\Generators\Creators\Database;

use Illuminate\Filesystem\Filesystem;
use Luminary\Services\Generators\Contracts\CreatorInterface;

class Migration implements CreatorInterface
{
    /**
     * Create a new migration at the given path.
     *
     * @param  string $name
     * @param  string $path
     * @param  string $table
     * @param  bool $create
     * @param bool $match
     * @return string
     */
    public static function create(string $name, string $path, $table = null, $create = false, $match = false) :string
    {
        $filesystem = new Filesystem;

        if (! $match || ! static::match($filesystem, $name, $path)) {
            return (static::migrationCreator($filesystem))->create($name, $path, $table, $create);
        }

        return '';
    }

    /**
     * Match the filename pattern
     *
     * @param Filesystem $files
     * @param string $name
     * @param string $path
     * @return string
     */
    protected static function match(Filesystem $files, string $name, string $path) :string
    {
        return ! empty($files->glob($path.'/*'.$name.'.php'));
    }

    /**
     * Set the stub path for the
     * Illuminate Migration Creator
     * and return a new instance
     *
     * @param Filesystem $filesystem
     * @return mixed
     */
    protected static function migrationCreator(Filesystem $filesystem)
    {
        return new class($filesystem) extends \Illuminate\Database\Migrations\MigrationCreator {
            public function stubPath()
            {
                return __DIR__.'/stubs';
            }
        };
    }
}
