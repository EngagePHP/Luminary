<?php

namespace Luminary\Services\Generators\Creators\Console;

use Luminary\Services\Generators\Creators\StubCreator;

class Kernel extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'commands' => '',
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__ . '/stubs/kernel.stub';
    }

    /**
     * Replace the attributes in a stub file
     *
     * @param string $stub
     * @return mixed
     */
    protected function replaceAttributes(string $stub)
    {
        $this->formatCommands();

        return parent::replaceAttributes($stub);
    }

    /**
     * Format the command array attribute for
     * replacing in the stub
     */
    protected function formatCommands() :void
    {
        $commands = $this->getAttribute('commands');

        // No point in parsing empty commands
        if (empty($commands)) {
            return;
        }

        // Lets turn the string into an array
        if (is_string($commands)) {
            $commands = explode(',', $commands);
        }

        //Add proper spacing and quotes for each class
        $commands = array_map(function ($command) {
            $command = trim($command);
            $command = preg_replace('/(^[\"\'\s]|[\"\'\s]$)/', '', $command);
            $command = preg_match("/::class/i", $command) ? $command : '\''.$command.'\'';

            return "        " . trim($command);
        }, $commands);

        // Create the final formatted output
        $commands = PHP_EOL . implode(',' . PHP_EOL, $commands) . PHP_EOL . "    ";

        $this->setAttribute('commands', $commands);
    }
}
