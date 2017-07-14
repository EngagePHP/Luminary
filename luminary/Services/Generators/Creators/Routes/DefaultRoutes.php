<?php

namespace Luminary\Services\Generators\Creators\Routes;

use Luminary\Services\Generators\Creators\StubCreator;

class DefaultRoutes extends StubCreator
{
    /**
     * Set the route name
     * as lowercase
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        $this->name = 'routes';
    }

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
