<?php

namespace Luminary\Services\Generators\Creators\Tests;

use Luminary\Services\Generators\Creators\StubCreator;

class TestCase extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/test-case.stub';
    }
}
