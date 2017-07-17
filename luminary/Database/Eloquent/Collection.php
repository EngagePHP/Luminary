<?php

namespace Luminary\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Luminary\Services\ApiResponse\ResponseCollectionTrait;

class Collection extends EloquentCollection
{
    use ResponseCollectionTrait;
}
