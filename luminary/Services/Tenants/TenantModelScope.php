<?php

namespace Luminary\Services\Tenants;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Luminary\Services\Tenants\Exceptions\TenantNotSetException;

class TenantModelScope implements Scope
{
    /**
     * Manually set the tenant id
     *
     * @var mixed int|null
     */
    protected static $tenantId = null;

    /**
     * Set a temporary id
     *
     * @var mixed int|null
     */
    protected $tempTenant = null;

    /**
     * Override the scope
     *
     * @var boolean
     */
    protected static $override = false;

    /**
     * Remove the scope
     *
     * @var boolean
     */
    protected $removed = false;

    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = [
        'RemoveTenant',
        'ByTenant',
        'AllTenants'
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) :void
    {
        if (static::getOverride() === false && $this->isRemoved() === false) {
            $id = $this->tenantId();
            $this->applyTenant($builder, $model, $id);
            $this->setEagerLoads($builder, $id);
        }

        // Extend the Scope
        $this->extend($builder);
    }

    protected function setEagerLoads(Builder $builder, $tenantId)
    {
        $relations = $this->mapEagerLoads($builder->getEagerLoads(), $tenantId);
        $builder->setEagerLoads($relations);
    }

    public function mapEagerLoads(array $eagerLoads, $tenantId) :array
    {
        return collect($eagerLoads)->map(
            function($closure) use($tenantId) {
                return function($query) use($closure, $tenantId) {
                    $builder = $query->getQuery();

                    if($builder->getMacro('byTenant')) {
                        $builder->byTenant($tenantId);
                    }

                    $closure($query);
                };
            }
        )->all();
    }

    /**
     * Add the no-tenant extension to the builder.
     * Remove the Tenant scope.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addRemoveTenant(Builder $builder) :void
    {
        $builder->macro('withoutTenant', function (Builder $builder) {
            $this->remove($builder);
            return $builder;
        });
    }

    /**
     * Add the all-tenants extension to the builder.
     * Alias for removeTenant();
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addAllTenants(Builder $builder) :void
    {
        $this->addRemoveTenant($builder);
    }

    /**
     * Add the where-tenant extension to the builder.
     * Include an array of specific tenant ids.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addByTenant(Builder $builder) :void
    {
        $builder->macro('byTenant', function (Builder $builder, $id) {
            $this->tempTenant = $id;
            return $builder;
        });
    }

    /**
     * Apply the tenant scope to the query
     *
     * @param Builder $builder
     * @param Model $model
     * @param $tenantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyTenant(Builder $builder, Model $model, $tenantId) :Builder
    {
        $where = (is_array($tenantId)) ? 'whereIn' : 'where';
        $column = $this->getQualifiedTenantColumn($model);

        $builder->{$where}($column, $tenantId);

        return $builder;
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder) :void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the name of the "tenant id" column.
     *
     * @return string
     */
    protected function getTenantColumn() :string
    {
        return 'tenant_id';
    }

    /**
     * Get the fully qualified "tenant id" column.
     *
     * @param Model $model
     * @return string
     */
    protected function getQualifiedTenantColumn(Model $model) :string
    {
        return $model->getTable().'.'.$this->getTenantColumn();
    }

    /**
     * Get the set Tenant Id.
     *
     * @return int|null
     */
    public function tenantId()
    {
        return $this->tempTenant ?: static::getTenantId();
    }

    /**
     * Get the current Tenant Id.
     *
     * @return int
     */
    public static function getTenantId() :int
    {
        return (int) trim(static::$tenantId);
    }

    /**
     * Manually set a Tenant Id.
     *
     * TenantScope::setTenantId(1);
     *
     * @param  int $tenantId
     * @return void
     */
    public static function setTenantId($tenantId) :void
    {
        static::$tenantId = $tenantId;
    }

    /**
     * Get the override state.
     *
     * @return bool
     */
    public static function getOverride() :bool
    {
        return static::$override;
    }

    /**
     * Set the override state.
     *
     * TenantScope::setOverride();
     * @param bool $bool
     * @return void
     */
    public static function setOverride(bool $bool = true) :void
    {
        static::$override = $bool;
    }

    /**
     * Determine if the given where clause is a tenant constraint.
     *
     * @param  array   $where
     * @param  string  $column
     *
     * @return bool
     */
    protected function isTenantConstraint(array $where, $column) :bool
    {
        return $where['type'] == 'Basic' && $where['column'] == $column;
    }

    /**
     * Has the scope been removed?
     *
     * @return bool
     */
    public function isRemoved() :bool
    {
        return $this->removed;
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        $this->removed = true;
    }

    /**
     * Validate the tenant id is not null or empty
     *
     * @param $tenantId
     * @throws \Luminary\Services\Tenants\Exceptions\TenantNotSetException
     * @return bool
     */
    protected function validateTenantId($tenantId) :bool
    {
        if (empty($tenantId) || $tenantId < 1) {
            throw new TenantNotSetException('Tenant ID required');
        }

        return true;
    }
}
