<?php

namespace Luminary\Services\Generators\Creators\Sanitizers;

use Luminary\Services\Generators\Creators\StubCreator;

class Sanitizer extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/sanitizer.stub';
    }
}
