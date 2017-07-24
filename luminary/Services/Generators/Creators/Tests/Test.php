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
        'methodName' => []
    ];

    /**
     * The type of test
     *
     * @var string
     */
    protected $type;

    /**
     * Set the class name as
     * studly case
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        parent::setName($name.'_test');
        $this->setAttribute('methodName', studly_case($name));
    }

    /**
     * Set the root directory path
     *
     * @param $path
     * @return void
     */
    protected function setPath($path) :void
    {
        $dir = dirname($path);
        $name = basename($path);

        $name = studly_case(str_plural($name));
        $dir = $dir.'/'.$name;

        if (! empty($this->type)) {
            $type = studly_case($this->type);
            $dir = $dir.'/'.$type;
        }

        parent::setPath($dir);
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
