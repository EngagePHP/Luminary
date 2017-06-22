<?php

namespace Luminary\Services\Generators\Creators\Database;

use Luminary\Services\Generators\Creators\StubCreator;

class Factory extends StubCreator
{
    /**
     * An array of replacable attributes
     *
     * @var array
     */
    protected $attributes = [
        'modelNamespace' => '',
        'modelName' => ''
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/factory.stub';
    }
}
