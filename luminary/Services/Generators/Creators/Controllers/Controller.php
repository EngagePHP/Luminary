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
        'repositoryBasename' => '',
        'repositoryNamespace' => ''
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
        $base = $this->getAttribute('repositoryBasename');
        $namespace = $this->getAttribute('repositoryNamespace');

        $stub =  str_replace('DummyRepositoryNamespace', $namespace, $stub);
        $stub =  str_replace('DummyRepositoryClass', $base, $stub);

        return $stub;
    }
}
