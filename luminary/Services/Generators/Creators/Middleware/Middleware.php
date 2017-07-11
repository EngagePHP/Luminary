<?php

namespace Luminary\Services\Generators\Creators\Middleware;

use Luminary\Services\Generators\Creators\StubCreator;

class Middleware extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/middleware.stub';
    }
}
