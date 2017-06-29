<?php

namespace Luminary\Services\ApiQuery\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Luminary\Services\ApiQuery\Fields\BuilderTrait as FieldsBuilderTrait;
use Luminary\Services\ApiQuery\Pagination\BuilderTrait as PaginationBuilderTrait;

class Builder extends EloquentBuilder
{
    use FieldsBuilderTrait;
    use PaginationBuilderTrait;
}
