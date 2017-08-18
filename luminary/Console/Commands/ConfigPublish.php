<?php

namespace Luminary\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\Filesystem\App\Storage;

class ConfigPublish extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'config:publish';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:publish 
        {key? : The config key to publish}
        {--force : Force overwrite of existing files}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish current configs to the api directory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->getKey();
        $path = app_path('config');
        $overwrite = $this->option('force') ? true : false;

        // Create the directory
        Storage::makeDirectory($path, true);

        // Copy the files
        collect($this->getConfig($key))->filter()->each(
            function ($file, $key) use ($path, $overwrite) {
                $new = $path . '/' . $key . '.php';

                if (! $overwrite && Storage::exists($new)) {
                    $this->comment(ucfirst($key) . ' config already exists. use --force to replace');
                    return;
                }

                Storage::copy($file, $new);
                $this->info(ucfirst($key) . ' config published successfully');
            }
        );
    }

    /**
     * Get the base key
     *
     * @return null|string
     */
    public function getKey()
    {
        $key = $this->argument('key');
        return $key ? strtok($key, '.') : null;
    }

    /**
     * Get a specific config file or
     * all keyed by name
     *
     * @param string|null $key
     * @return array
     */
    public function getConfig(string $key = null) :array
    {
        $configs = $this->configs();

        return is_null($key) ? $configs : [$key => array_get($configs, $key)];
    }

    /**
     * Get all config file locations
     * keyed by name
     *
     * @return array
     */
    public function configs()
    {
        return array_merge(
            $this->lumenConfigs(),
            $this->luminaryConfigs()
        );
    }

    /**
     * Return the default lumen configs
     *
     * @return array
     */
    public function lumenConfigs() :array
    {
        return $this->getConfigFiles(
            base_path('vendor/laravel/lumen-framework/config')
        );
    }

    /**
     * Return all of the luminary configs
     *
     * @return array
     */
    public function luminaryConfigs() :array
    {
        return $this->getConfigFiles(
            base_path('config')
        );
    }

    /**
     * Get the list of files
     * keyed by name from a given path
     *
     * @param string $path
     * @return array
     */
    public function getConfigFiles(string $path) :array
    {
        $files = Storage::files($path);

        return collect($files)->keyBy(function ($file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            return basename($file, '.' . $ext);
        })->all();
    }
}
