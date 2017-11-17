<?php

$router->group(
    ['namespace' => 'Luminary\Services\Auth\Controllers', 'prefix' => 'auth'],
    function ($router) {

        // The login route
        $router->post('login', 'AuthController@login');

        // Authenticated Routes
        $router->group(
            ['middleware' => 'jwt.auth'],
            function ($router) {
                $router->post('logout', 'AuthController@logout');
                $router->post('refresh', 'AuthController@refresh');
            }
        );

        // User Route
        $router->group(
            ['middleware' => ['jwt.auth', 'request', 'response']],
            function ($router) {
                $router->get('user', 'AuthController@user');
            }
        );
    }
);
