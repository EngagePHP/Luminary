<?php

namespace Luminary\Services\Generators\Creators\Database;

use Luminary\Services\Generators\Creators\StubCreator;

class Seeder extends StubCreator
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/seeder.stub';
    }
}
