<?php

$router->group(['namespace' => 'Luminary\Events', 'middleware' => ['hook'],  'prefix' => 'events'],
    function ($router) {
        // Trigger an event
        $router->post('/', [
            'as' => 'events',
            'uses' => 'EventController@trigger'
        ]);
    }
);
