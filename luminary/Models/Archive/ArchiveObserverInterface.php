<?php

namespace Luminary\Models\Archive;

use Illuminate\Database\Eloquent\Model;

interface ArchiveObserverInterface
{
    /**
     * Trigger the archived event
     *
     * @param Model $model
     * @return void
     */
    public function triggerArchived(Model $model): void;

    /**
     * Trigger the unarchived event
     *
     * @param Model $model
     * @return void
     */
    public function triggerUnarchived(Model $model): void;
}