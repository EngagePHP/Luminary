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
        'namespace' => '',
        'slug' => ''
    ];

    /**
     * Set the route name
     * as lowercase
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        $this->name = 'routes';
    }

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
