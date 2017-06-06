<?php

namespace Luminary\Services\Generators\Creators\Routes;

use Luminary\Services\Generators\Creators\StubCreator;

class Routes extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'controller' => '',
        'slug' => ''
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/routes.stub';
    }
}
