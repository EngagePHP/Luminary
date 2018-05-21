<?php

namespace Luminary\EventHooks;

use Illuminate\Http\Request;
use Luminary\Http\Controllers\Controller;

class EventController extends Controller
{
    /**
     * Trigger an API event
     */
    public function trigger(Request $request)
    {
        $input = $request->all();
        $event = array_get($input, 'event');
        $data = array_get($input, 'data');

        event(new $event($data));
    }
}
