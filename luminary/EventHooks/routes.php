<?php

$router->group(
    ['namespace' => 'Luminary\EventHooks', 'middleware' => ['hook'],  'prefix' => 'event-hooks'],
    function ($router) {
        // Trigger an event
        $router->post('/', [
            'as' => 'events',
            'uses' => 'EventController@trigger'
        ]);
    }
);
