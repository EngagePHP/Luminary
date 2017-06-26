<?php

namespace Luminary\Services\Generators\Creators\Tests;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Creators\StubCreator;

class ResourceTest extends Test
{
    /**
     * An array of replacable attributes
     *
     * @var array
     */
    protected $attributes = [
        'slug' => '',
        'response' => '\'\''
    ];

    /**
     * The type of test
     *
     * @var string
     */
    protected $type = 'resource';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/test-resource.stub';
    }
}
