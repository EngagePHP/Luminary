<?php

namespace Luminary\Services\ApiLoader\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\ApiLoader\Helpers\Cache;

class LuminaryCache extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'luminary:cache';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'luminary:cache
        {--clear : Whether the job should be queued}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the luminary API Loader';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->option('clear') ? $this->clearCache() : $this->createCache();
    }

    protected function clearCache()
    {
        Cache::clear();

        $this->info('Api Loader Cache Cleared');
    }

    protected function createCache()
    {
        Cache::create();

        $this->info('Api Loader Cached successfully');
    }
}
