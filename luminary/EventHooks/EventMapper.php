<?php

namespace Luminary\EventHooks;

class EventMapper
{
    /**
     * The mapped events
     *
     * @var array
     */
    protected $events = [];

    /**
     * The EventMapper Constructor
     *
     * @param array $events
     */
    public function __coonstruct(array $events = []) :void
    {
        $this->merge($events);
    }

    /**
     * Get the array of all events
     *
     * @return array
     */
    public function all() :array
    {
        return $this->events;
    }

    /**
     * Get a named event
     *
     * @param $name
     * @return string | null
     */
    public function find($name)
    {
        return $this->get($name);
    }

    /**
     * Get a named event
     *
     * @param $name
     * @return string | null
     */
    public function get($name)
    {
        return array_get($this->events, $name);
    }

    /**
     * Add an event to the Event Mapper
     *
     * @param $name
     * @param $class
     * @return EventMapper
     */
    public function map($name, $class) :EventMapper
    {
        return $this->put($name, $class);
    }

    /**
     * Add an event to the Event Mapper
     *
     * @param $name
     * @param $class
     * @return EventMapper
     */
    public function put($name, $class) :EventMapper
    {
        $this->events[$name] = $class;

        return $this;
    }

    /**
     * Add multiple events to the Event Mapper
     *
     * @param array $events
     * @return EventMapper
     */
    public function merge(array $events = []) :EventMapper
    {
        foreach ($events as $name => $class) {
            $this->put($name, $class);
        }

        return $this;
    }
}
