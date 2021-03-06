<?php

namespace Luminary\EventHooks;

class Event
{
    /**
     * Call the singleton event methods statically
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $mapper = app(EventMapper::class);
        return call_user_func_array([$mapper, $name], $arguments);
    }
}
