<?php

namespace Luminary\Services\Generators\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\Generators\Entity\Scaffold;

class EntityCreator extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:entity';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:entity {name : Name of the entity to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Entity';

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

        Scaffold::create($name, app_path('Entities/'.$name));
    }
}
