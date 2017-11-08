<?php

namespace Luminary\Services\Generators\Creators\Authorizers;

use Luminary\Services\Generators\Creators\StubCreator;

class Authorizer extends StubCreator
{
    protected static $model;

    /**
     * Create a new seed at the given path.
     *
     * @param string $name
     * @param string $path
     * @param null $model
     * @param array $attributes
     * @return StubCreator
     */
    public static function create(string $name, string $path, $model = null, array $attributes = []) :StubCreator
    {
        static::$model = $model;

        $self = parent::create($name, $path, $attributes);

        static::$model = null;

        return $self;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/authorizer.stub';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @return \Luminary\Services\Generators\Creators\StubCreator $this
     */
    protected function replaceNamespace(string &$stub) :StubCreator
    {
        parent::replaceNamespace($stub);

        $stub = str_replace('ModelClassNamespace', static::$model, $stub);

        return $this;
    }
}
