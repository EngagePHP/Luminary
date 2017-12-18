<?php

namespace Luminary\Services\Generators\Creators\Policies;

use Luminary\Services\Generators\Creators\StubCreator;

class PolicyRegistrar extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'model' => '',
        'policy' => '',
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/policy-registrar.stub';
    }
}
