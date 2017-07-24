<?php

namespace Luminary\Services\Generators\Console\Commands;

use Illuminate\Console\Command;
use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Creators\Routes\DefaultRoutes;
use Luminary\Services\Generators\Creators\Tests\PhpUnit;
use Luminary\Services\Generators\Creators\Tests\ResourceTest;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LuminaryScaffold extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'luminary:scaffold';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'luminary:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the default Luminary folder structure';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $args = [
            $this->ask("Package name (<luminary>/<api>)"),
            $this->ask("please provide a brief description of the api", " "),
            $this->ask("Author Name"),
            $this->ask("Author Email")
        ];

        $this->composerInit(...$args);
        $this->composerAutoloadDev();
        $this->createDirectories();
        $this->createPreCommit();
        $this->createReadme();
        $this->createTests();
        $this->createDefault();
        $this->createDefaultTests();
    }

    /**
     * Create a default composer.json file
     *
     * @param $name
     * @param $description
     * @param $author_name
     * @param $author_email
     * @return void
     */
    protected function composerInit(string $name, string $description, string $author_name, string $author_email) :void
    {
        $path = app_path();

        $command = '/opt/composer/composer.phar init';
        $command .= " --name='$name'";
        $command .= " --description='$description'";
        $command .= " --author='$author_name <$author_email>'";
        $command .= $this->composerRequireDev();

        $process = new Process("cd $path && $command");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info($process->getOutput());
    }

    /**
     * Add require-dev packages to the
     * composer.json
     *
     * @return string
     */
    protected function composerRequireDev() :string
    {
        $require = [
            "engage-php/luminary:0.*",
            "squizlabs/php_codesniffer:3.*",
            "fzaninotto/faker:~1.4",
            "phpunit/phpunit:~5.0",
            "mockery/mockery:~0.9"
        ];

        return " --require-dev=" . implode(" --require-dev=", $require);
    }

    /**
     * Add the AutoloadDev requirements for testing
     *
     * @return void
     */
    protected function composerAutoloadDev() :void
    {
        $file = app_path('composer.json');
        $contents = Storage::get($file);

        $json = json_decode($contents, true);
        $json['autoload-dev'] = [
            'classmap' => [
                'tests/'
            ]
        ];
        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        Storage::put($file, $json);
    }

    /**
     * Create default directory structure
     *
     * @return void
     */
    protected function createDirectories() :void
    {
        $directories = ['Entities', 'Resources', 'Services'];

        foreach ($directories as $directory) {
            $directory = app_path($directory);

            Storage::makeDirectory($directory, true);
            Storage::put($directory . '/' . '.gitkeep', "");
        }
    }

    /**
     * Create default resource routes
     *
     * @return void
     */
    protected function createDefault() :void
    {
        $path = app_path('Resources/Default');
        Storage::makeDirectory($path, true);

        DefaultRoutes::create('routes', $path);
    }

    /**
     * Create and empty Readme file
     * in the root directory
     *
     * @return void
     */
    protected function createReadme() :void
    {
        $path = app_path('readme.md');

        Storage::put($path, "");
    }

    /**
     * Create the git pre-commit files
     * in the root directory
     *
     * @return void
     */
    protected function createPreCommit() :void
    {
        $this->preCommitConfig();
        $this->preCommitSetup();
    }

    /**
     * Create the git pre-commit config file
     *
     * @return void
     */
    protected function preCommitConfig() :void
    {
        $url = "https://raw.githubusercontent.com/EngagePHP/pre-commit/master/PHP/.pre-commit-config.yaml";
        $content = file_get_contents($url);
        $path = app_path('.pre-commit-config.yaml');

        Storage::put($path, $content);
    }

    /**
     * Create the setup bash script
     *
     * @return void
     */
    protected function preCommitSetup() :void
    {
        $url = "https://raw.githubusercontent.com/EngagePHP/pre-commit/master/setup.sh";
        $content = file_get_contents($url);
        $path = app_path('setup.sh');

        Storage::put($path, $content);
    }

    /**
     * Create the default tests and folder
     *
     * @return void
     */
    protected function createTests() :void
    {
        $directory = app_path('tests');

        Storage::makeDirectory($directory, true);
        PhpUnit::create('phpunit', app_path());
    }

    /**
     * Create the default tests and folder
     *
     * @return void
     */
    protected function createDefaultTests() :void
    {
        $target = app_path('Resources/Default/Tests');
        $directory = app_path('tests/Default');

        Storage::makeDirectory($directory, true);
        ResourceTest::create('Default', $directory, ['response' => '$this->app->version()'])->link($target);
    }
}
