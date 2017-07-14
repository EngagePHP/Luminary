<?php

namespace Luminary\Services\Generators\Creators\Controllers;

use Luminary\Services\Generators\Creators\StubCreator;

class Controller extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'requestBasename' => '',
        'requestNamespace' => ''
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/controller.stub';
    }

    /**
     * Replace the attributes in a stub file
     *
     * @param string $stub
     * @return mixed
     */
    protected function replaceAttributes(string $stub)
    {
        $base = $this->getAttribute('requestBasename');
        $namespace = $this->getAttribute('requestNamespace');

        $stub =  str_replace('DummyRequestNamespace', $namespace, $stub);
        $stub =  str_replace('DummyRequestClass', $base, $stub);

        return $stub;
    }
}
