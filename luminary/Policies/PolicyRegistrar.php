<?php

namespace Luminary\Policies;

use Luminary\Database\Eloquent\Model;

abstract class PolicyRegistrar
{
    /**
     * The Policies permissions
     * to sync with the database
     *
     * @var array
     */
    public $permissions = [];

    /**
     * The Policies to register
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Get the array of event subscribers
     *
     * @return array
     */
    public function policies() :array
    {
        return $this->policies;
    }

    /**
     * Get the permissions array
     *
     * @return array
     */
    public function permissions() :array
    {
        return $this->permissions;
    }

    /**
     * Get a policy registrar
     * model instance
     *
     * @return Model
     */
    abstract public function model();

    /**
     * Get the policy registrar model type
     *
     * @return string
     */
    public function type()
    {
        return $this->model()->getType();
    }
}
