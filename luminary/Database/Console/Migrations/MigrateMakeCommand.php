<?php

namespace Luminary\Database\Console\Migrations;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as LaravelMigrateMakeCommand;
use Luminary\Services\Filesystem\App\Storage;

class MigrateMakeCommand extends LaravelMigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration {entity : The entity for the migration.}
        {name : The name of the migration.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}
        {--singular : Keep the name singular}';

    /**
     * Get migration path (specified by the entity name).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $entity = trim($this->input->getArgument('entity'));
        $entity = $this->option('singular') ? str_singular($entity) : str_plural($entity);
        $entity = studly_case($entity);

        $dir = $this->laravel->basePath() . '/api/Entities/' . $entity . '/Database/Migrations';

        Storage::makeDirectory($dir, true);

        return $dir;
    }
}