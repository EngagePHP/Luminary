<?php

namespace Luminary\Services\Generators\Creators\Authorizers;

use Luminary\Services\Generators\Creators\StubCreator;

class Authorizer extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/authorizer.stub';
    }
}
