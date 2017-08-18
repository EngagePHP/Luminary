<?php

namespace Luminary\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Debug\Dumper;

class Config extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'config';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config {key? : The config key to show values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show config values';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dumpConfig($this->argument('key'));
    }

    /**
     * Dump the config values
     *
     * @param string|null $key
     */
    protected function dumpConfig(string $key = null)
    {
        (new Dumper)->dump(config($key));
    }
}
