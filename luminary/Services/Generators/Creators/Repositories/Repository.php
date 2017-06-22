<?php

namespace Luminary\Services\Generators\Creators\Repositories;

use Luminary\Services\Generators\Creators\StubCreator;

class Repository extends StubCreator
{
    /**
     * An array of replaceable attributes
     *
     * @var array
     */
    protected $attributes = [
        'model' => ''
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/repository.stub';
    }

    /**
     * Replace the attributes in a stub file
     *
     * @param string $stub
     * @return mixed
     */
    protected function replaceAttributes(string $stub)
    {
        $model = $this->getAttribute('model');
        $modelArr = explode('\\', $model);
        $name = array_pop($modelArr);

        $stub =  str_replace('ModelNamespace', $model, $stub);
        $stub =  str_replace('ModelClass', $name, $stub);

        return $stub;
    }
}
