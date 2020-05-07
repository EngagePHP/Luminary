<?php

namespace Luminary\Models\Expire;

use Illuminate\Database\Eloquent\Model;

class ExpireObserver
{
    /**
     * Listen for the BillingContract saving event
     *
     * @param Model $model
     */
    public function saving(Model $model)
    {
        if($this->hasBeenExpired($model)) {
            $model->triggerExpiring('expiring');
        }

        if($this->hasBeenUnexpired($model)) {
            $model->triggerUnexpiring('unexpiring');
        }
    }

    /**
     * Listen for the BillingContract saving event
     *
     * @param Model $model
     */
    public function saved(Model $model)
    {
        if($model->expired) {
            $model->triggerExpired('expired');
        }

        if($model->unexpired) {
            $model->triggerUnexpired('unexpired');
        }
    }

    /**
     * Check if the contract was completed
     *
     * @param Model $model
     * @return bool
     */
    protected function hasBeenExpired(Model $model) :bool
    {
        $expiredColumn = $model->getExpiredAtColumn();
        $check = [
            $model->isDirty($expiredColumn),
            !is_null($model->{$expiredColumn}),
            is_null($model->getOriginal($expiredColumn))
        ];

        return count(array_filter($check)) === count($check);
    }

    /**
     * Check if the contract was completed
     *
     * @param Model $model
     * @return bool
     */
    protected function hasBeenUnexpired(Model $model) :bool
    {
        $expiredColumn = $model->getExpiredAtColumn();
        $check = [
            $model->isDirty($expiredColumn),
            is_null($model->{$expiredColumn}),
            !is_null($model->getOriginal($expiredColumn))
        ];

        return count(array_filter($check)) === count($check);
    }
}