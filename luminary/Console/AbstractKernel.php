<?php

namespace Luminary\Console;

use Luminary\Contracts\Console\Kernel as LuminaryConsoleKernel;

abstract class AbstractKernel implements LuminaryConsoleKernel
{
    /**
     * A list of console commands to register
     *
     * @return array
     */
    protected $commands = [];

    /**
     * Return the console commands to register
     *
     * @return array
     */
    public function commands() :array
    {
        return $this->commands;
    }
}
