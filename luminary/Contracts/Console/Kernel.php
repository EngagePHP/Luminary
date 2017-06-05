<?php

namespace Luminary\Contracts\Console;

use Illuminate\Console\Scheduling\Schedule;

interface Kernel
{
    /**
     * Return the console commands to register
     *
     * @return array
     */
    public function commands() :array;

    /**
     * Schedule commands with the application framework
     *
     * @param Schedule $schedule
     */
    public function schedule(Schedule $schedule) :void;
}
