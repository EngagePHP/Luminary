<?php

namespace Luminary\Services\Tenants;

trait ByTenant
{
    /**
     * Boot the ByTenant Model Trait
     *
     * @return void
     */
    public static function bootByTenant() :void
    {
        static::addGlobalScope(new TenantModelScope);
        static::observe(TenantModelObserver::class);
    }

    /**
     * Returns a new builder without the Tenant scope applied.
     *
     * $allUsers = User::withoutTenant()->get();
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutTenant()
    {
        return (new static)->newQueryWithoutScope(new TenantModelScope);
    }

    /**
     * Set the Tenant id for the scope
     *
     * @param int $id
     */
    public static function setTenantId(int $id)
    {
        TenantModelScope::setTenantId($id);
    }

    /**
     * Eager load relations on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations)
    {
        $scope = $this->getCurrentTenantScope();
        $tenantId = $scope->tenantId();
        $relations = is_string($relations) ? func_get_args() : $relations;
        $relations = collect($relations)->mapWithKeys(
            function($constraints, $name) {
                if (is_numeric($name)) {
                    $name = $constraints;
                    $constraints = function () {};
                }

                return [$name => $constraints];
            }
        )->all();
        $relations = $scope->mapEagerLoads($relations, $tenantId);

        return parent::load($relations);
    }

    /**
     * Get the current Tenant Model Scope
     *
     * @return TenantModelScope
     */
    public function getCurrentTenantScope() :TenantModelScope
    {
        return $this->getGlobalScope(TenantModelScope::class);
    }
}
