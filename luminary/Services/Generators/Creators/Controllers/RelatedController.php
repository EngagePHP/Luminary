<?php

namespace Luminary\Services\Generators\Creators\Controllers;

class RelatedController extends Controller
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/related-controller.stub';
    }
}
