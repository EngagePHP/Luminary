<?php

namespace Luminary\Services\Generators\Creators\Tests;

use Luminary\Services\Generators\Creators\StubCreator;

class PhpUnit extends StubCreator
{

    /**
     * Set the route name
     * as lowercase
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        $this->name = 'phpunit';
    }

    /**
     * Get the root path or a path
     * from the root path
     *
     * @param null $name
     * @return string
     */
    protected function getPath($name = null) :string
    {
        $file = $name ? '/'.$this->name.'.xml' : '';
        return $this->path.$file;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/phpunit.stub';
    }
}
