<?php

namespace Luminary\Database;

use Illuminate\Database\Seeder;

abstract class EnvironmentSeeder extends Seeder
{
    /**
     * CRun the database seeds
     * based on application environment
     *
     * @return void
     */
    public function run()
    {
        $environment = app()->environment();
        $output = $this->command->getOutput();

        if (method_exists($this, $environment)) {
            $this->$environment();
            $output->writeln("<info>Environment:</info> $environment");
        } else {
            $output->writeln("nothing to seed");
        }
    }
}
