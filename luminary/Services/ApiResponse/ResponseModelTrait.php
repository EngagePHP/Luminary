<?php

namespace Luminary\Services\ApiResponse;

use Luminary\Database\Eloquent\Collection;

trait ResponseModelTrait
{
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
}
