<?php

namespace Luminary\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Luminary\Console\Commands\RouteList;
use Luminary\Services\ApiLoader\Console\Commands\LuminaryCache;
use Luminary\Console\Commands\Config;
use Luminary\Console\Commands\ConfigPublish;
use Luminary\Services\Generators\Console\Commands\EntityCreator;
use Luminary\Services\Generators\Console\Commands\LuminaryScaffold;
use Luminary\Services\Generators\Console\Commands\ResourceCreator;
use Luminary\Services\Generators\Console\Commands\ServiceCreator;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Config::class,
        ConfigPublish::class,
        LuminaryCache::class,
        LuminaryScaffold::class,
        EntityCreator::class,
        ResourceCreator::class,
        ServiceCreator::class,
        RouteList::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
