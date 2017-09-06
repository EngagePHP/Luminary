<?php

namespace Luminary\Services\Generators\Creators\Requests;

use Luminary\Services\Generators\Creators\StubCreator;

class Request extends StubCreator
{
    /**
     * Use the validator stub
     *
     * @var bool
     */
    protected static $validate;

    /**
     * Create a new seed at the given path.
     *
     * @param string $name
     * @param string $path
     * @param array $attributes
     * @param bool $validate
     * @return StubCreator
     */
    public static function create(
        string $name,
        string $path,
        array $attributes = [],
        bool $validate = true
    ) :StubCreator {
        static::$validate = $validate;
        parent::create($name, $path, $attributes);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return $this->validate ? __DIR__.'/stubs/request.stub' : __DIR__.'/stubs/auth-request.stub';
    }
}
