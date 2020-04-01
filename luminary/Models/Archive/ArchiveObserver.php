<?php

namespace Luminary\Models\Archive;

use Illuminate\Database\Eloquent\Model;

class ArchiveObserver
{
    /**
     * Listen for the BillingContract saving event
     *
     * @param Model $model
     */
    public function saving(Model $model)
    {
        if($this->hasBeenArchived($model)) {
            $model->triggerArchiving('archiving');
        }

        if($this->hasBeenUnarchived($model)) {
            $model->triggerUnarchiving('unarchiving');
        }
    }

    /**
     * Listen for the BillingContract saving event
     *
     * @param Model $model
     */
    public function saved(Model $model)
    {
        if($model->archived) {
            $model->triggerArchived('archived');
        }

        if($model->unarchived) {
            $model->triggerUnarchived('unarchived');
        }
    }

    /**
     * Check if the contract was completed
     *
     * @param Model $model
     * @return bool
     */
    protected function hasBeenArchived(Model $model) :bool
    {
        $check = [
            $model->isDirty('archived_at'),
            !is_null($model->archived_at),
            is_null($model->getOriginal('archived_at'))
        ];

        return count(array_filter($check)) === count($check);
    }

    /**
     * Check if the contract was completed
     *
     * @param Model $model
     * @return bool
     */
    protected function hasBeenUnarchived(Model $model) :bool
    {
        $check = [
            $model->isDirty('archived_at'),
            is_null($model->archived_at),
            !is_null($model->getOriginal('archived_at'))
        ];

        return count(array_filter($check)) === count($check);
    }
}