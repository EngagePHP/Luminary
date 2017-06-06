<?php

namespace Luminary\Services\Generators\Creators\Console;

use Luminary\Services\Generators\Creators\StubCreator;

class Command extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'name' => 'namespace:command',
        'signature' => 'namespace:command',
        'description' => 'This command does not have a description'
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/command.stub';
    }
}
