<?php

namespace Luminary\Services\Generators\Creators\Models;

use Luminary\Services\Generators\Creators\StubCreator;

class Model extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'table' => ''
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/model.stub';
    }
}
