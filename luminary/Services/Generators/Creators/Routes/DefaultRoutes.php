<?php

namespace Luminary\Services\Generators\Creators\Routes;

use Luminary\Services\Generators\Creators\StubCreator;

class DefaultRoutes extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/default-routes.stub';
    }
}
