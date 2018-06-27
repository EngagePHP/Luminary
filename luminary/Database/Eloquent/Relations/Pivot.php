<?php

namespace Luminary\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Relations\Pivot as LumenPivot;
use Luminary\Database\Eloquent\ModelTrait;

class Pivot extends LumenPivot
{
    use ModelTrait;

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        if ($this->pivotParent) {
            return parent::getUpdatedAtColumn();
        }

        return static::UPDATED_AT;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        if ($this->pivotParent) {
            return parent::getCreatedAtColumn();
        }

        return static::CREATED_AT;
    }
}