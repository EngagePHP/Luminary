<?php

namespace Luminary\Services\Generators\Creators\Providers;

use Luminary\Services\Generators\Creators\StubCreator;

class ServiceProvider extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/service-provider.stub';
    }
}
