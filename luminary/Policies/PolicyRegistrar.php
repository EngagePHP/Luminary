<?php

namespace Luminary\Policies;

class PolicyRegistrar
{
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
}
