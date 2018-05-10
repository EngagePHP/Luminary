<?php

namespace Luminary\EventHooks;

class EventRegistrar
{
    /**
     * The event handler mappings
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Mapp Events to names for
     * quick dispatching
     *
     * @var array
     */
    protected $map = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Get the array of event listeners
     *
     * @return array
     */
    public function listeners() :array
    {
        return $this->listen;
    }

    /**
     * Return the list of mapped event dispatchers
     *
     * @return array
     */
    public function mapped() :array
    {
        return $this->map;
    }

    /**
     * Get the array of event subscribers
     *
     * @return array
     */
    public function subscribers() :array
    {
        return $this->subscribe;
    }
}
