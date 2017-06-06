<?php

namespace Luminary\Services\Generators\Creators\Requests;

use Luminary\Services\Generators\Creators\StubCreator;

class Request extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/request.stub';
    }
}
