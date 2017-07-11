<?php

namespace Luminary\Services\Generators\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\Generators\Resource\Scaffold;

class ResourceCreator extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:resource';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:resource {name : Name of the resource to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Resource';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = str_plural($name);
        $studly = studly_case($name);

        Scaffold::create($name, app_path('Resources/'.$studly));
    }
}
