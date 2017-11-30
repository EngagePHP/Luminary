<?php

namespace Luminary\Services\Generators\Creators\Policies;

use Luminary\Services\Generators\Creators\StubCreator;

class Policy extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/policy.stub';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @return \Luminary\Services\Generators\Creators\StubCreator $this
     */
    protected function replaceNamespace(string &$stub) :StubCreator
    {
        parent::replaceNamespace($stub);

        $singular = str_singular($this->name);
        $stub = str_replace('ModelClassNamespace', $this->rootNamespace() . '\\' . $singular, $stub);

        return $this;
    }
}
