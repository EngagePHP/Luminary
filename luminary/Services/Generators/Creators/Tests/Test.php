<?php

namespace Luminary\Services\Generators\Creators\Tests;

use Luminary\Services\Filesystem\App\Storage;
use Luminary\Services\Generators\Creators\StubCreator;

class Test extends StubCreator
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
     * Set the class name as
     * studly case
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        parent::setName($name.'_test');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/test.stub';
    }

    /**
     * Create a symlink for the test folder
     *
     * @param string $target
     * @return string
     */
    public function link(string $target) :string
    {
        return Storage::link($target, $this->path);
    }
}
