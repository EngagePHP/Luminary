<?php

namespace Luminary\Console;

use Illuminate\Console\Scheduling\Schedule as Schedule;

interface LuminarySchedule
{
    /**
     * Define additional application command schedules.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule) :void;
}
