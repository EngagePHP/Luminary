<?php

namespace Luminary\Services\Generators\Creators\Tests;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Creators\StubCreator;

class ServiceTest extends Test
{
    /**
     * The type of test
     *
     * @var string
     */
    protected $type = 'service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/test.stub';
    }
}
