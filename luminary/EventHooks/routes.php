<?php

$router->group(
    ['namespace' => 'Luminary\EventHooks', 'middleware' => ['jwt.auth', 'hook'],  'prefix' => 'event-hooks'],
    function ($router) {
        // Trigger an event
        $router->post('/', [
            'as' => 'events',
            'uses' => 'EventController@trigger'
        ]);
    }
);
