<?php

namespace Luminary\Services\Generators\Creators\Events;

use Luminary\Services\Generators\Creators\StubCreator;

class Registrar extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/registrar.stub';
    }
}
