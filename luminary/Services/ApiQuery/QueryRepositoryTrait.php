<?php

namespace Luminary\Services\ApiQuery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Luminary\Database\Eloquent\Model;
use Luminary\Models\Archive\ArchiveModelScope;

trait QueryRepositoryTrait
{
    /**
     * @param Builder $builder
     * @return mixed
     */
    static public function query(string $modelClass)
    {
        $modelClass::applyApiQueryScope();
    }
}
