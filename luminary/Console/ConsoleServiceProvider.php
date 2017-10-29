<?php

namespace Luminary\Console;

use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Laravel\Lumen\Console\ConsoleServiceProvider as LaravelConsoleServiceProvider;
use Luminary\Database\Console\Migrations\MigrateMakeCommand;

class ConsoleServiceProvider extends LaravelConsoleServiceProvider
{
    /**
     * Register the command.
     * @todo: Remove once laravel fixes this issue
     *
     * @return void
     */
    protected function registerCacheClearCommand()
    {
        $this->app->singleton('command.cache.clear', function ($app) {
            return new CacheClearCommand($app['cache'], $app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }
}
