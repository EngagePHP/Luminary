<?php

namespace Luminary\Services\ApiRequest;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait RequestRepositoryTrait
{
    static public function model()
    {
        $resource = app('request')->resource();
        $morphMap = Relation::morphMap();

        if($model = array_get($morphMap, $resource)) {
            return $model;
        }

        throw new ModelNotFoundException('resource entity not found');
    }
}
