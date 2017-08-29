<?php

namespace Luminary\Services\Generators\Creators\Repositories;

class RelatedRepository extends Repository
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() :string
    {
        return __DIR__.'/stubs/related-repository.stub';
    }
}
