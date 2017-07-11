<?php

namespace Luminary\Services\Generators\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\Generators\Service\Scaffold;

class ServiceCreator extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : Name of the service to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new luminary service';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = str_plural($name);
        $name = studly_case($name);

        Scaffold::create($name, app_path('Services/'.$name));
    }
}
