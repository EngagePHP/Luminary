<?php

namespace Luminary\Services\Generators\Creators\Policies;

use Illuminate\Filesystem\Filesystem;
use Luminary\Services\Generators\Creators\Database\Migration as LuminaryMigration;

class Migration extends LuminaryMigration
{
    /**
     * Create a new migration at the given path.
     *
     * @param  string $name
     * @param  string $path
     * @param  string $class
     * @param bool $create
     * @param bool $match
     * @return string
     */
    public static function create(string $name, string $path, $class = null, $create = true, $match = true) :string
    {
        return parent::create($name, $path, $class, $create, $match);
    }

    /**
     * Create a new migration for seeding default permissions to roles
     *
     * @param  string $name
     * @param  string $path
     * @param  string $class
     * @return string
     */
    public static function seed(string $name, string $path, $table = null) :string
    {
        return parent::create($name, $path, $table, false, true);
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
