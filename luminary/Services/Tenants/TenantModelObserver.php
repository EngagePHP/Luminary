<?php

namespace Luminary\Services\Tenants;

use Illuminate\Database\Eloquent\Model;

class TenantModelObserver
{
    /**
     * Listen to the Model saving event and
     * Add/update the tenant id
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function saving(Model $model)
    {
        $id = $model->getCurrentTenantScope()->tenantId();

        $model->setAttribute('tenant_id', $id);
    }
}
