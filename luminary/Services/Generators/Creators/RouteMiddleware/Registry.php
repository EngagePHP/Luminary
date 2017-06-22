<?php

namespace Luminary\Services\Generators\Creators\RouteMiddleware;

use Luminary\Services\Generators\Creators\StubCreator;

class Registry extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/registry.stub';
    }
}
