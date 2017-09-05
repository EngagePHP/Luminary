<?php

namespace Luminary\Services\Generators\Creators\Validators;

use Luminary\Services\Generators\Creators\StubCreator;

class Validator extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/validator.stub';
    }
}
