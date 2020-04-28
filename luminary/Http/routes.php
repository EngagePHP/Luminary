<?php

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'namespace' => 'Luminary\Http\Controllers',
    'prefix' => '{entity}',
    'middleware' => ['jwt.auth', 'request.headers', 'request.middleware','query','response']
],
    function ($router) {
        // Retrieve a list for entity
        $router->get('/', [
            'as' => 'entity',
            'uses' => 'Controller@index'
        ]);

        // Create a new entity for entity
        $router->post('/', [
            'as' => 'entity.store',
            'uses' => 'Controller@store'
        ]);

        // Show an entity for entity
        $router->get('/{id}', [
            'as' => 'entity.show',
            'uses' => 'Controller@show'
        ]);

        // Update an entity for entity
        $router->patch('/{id}', [
            'as' => 'entity.update',
            'uses' => 'Controller@update'
        ]);

        // Delte an entity for entity
        $router->delete('/{id}', [
            'as' => 'entity.destroy',
            'uses' => 'Controller@destroy'
        ]);
    }
);

// Relationship Controller
$router->group([
    'namespace' => 'Luminary\Http\Controllers',
    'prefix' => '{entity}/{id}',
    'middleware' => ['jwt.auth', 'request.headers', 'request.middleware','query','response']
],
    function ($router) {
        // Retrieve a entity relationship
        $router->get('/relationships/{relationship}', [
            'as' => 'entity.relationships',
            'uses' => 'RelationshipController@index'
        ]);

        // Update a entity relationship
        $router->post('/relationships/{relationship}', [
            'as' => 'entity.relationships.create',
            'uses' => 'RelationshipController@store'
        ]);

        // Update a entity relationship
        $router->patch('/relationships/{relationship}', [
            'as' => 'entity.relationships.update',
            'uses' => 'RelationshipController@update'
        ]);

        // Delete a entity relationship
        $router->delete('/relationships/{relationship}', [
            'as' => 'entity.relationships.delete',
            'uses' => 'RelationshipController@destroy'
        ]);
    }
);

// Related Controller
$router->group([
        'namespace' => 'Luminary\Http\Controllers',
        'prefix' => '{entity}/{id}',
        'middleware' => ['jwt.auth', 'request.headers', 'request.middleware','query','response']
    ],
    function ($router) {
        // Retrieve a entity relationship
        $router->get('/{related}', [
            'as' => 'entity.related',
            'uses' => 'RelatedController@index'
        ]);

        // Retrieve the customer relationship record
        $router->get('/{related}/{relatedId}', [
            'as' => 'entity.related.show',
            'uses' => 'RelatedController@show'
        ]);

        // Update a entity relationship
        $router->post('/{related}', [
            'as' => 'entity.related.create',
            'uses' => 'RelatedController@store'
        ]);

        // Update a entity relationship
        $router->patch('/{related}/{relatedId}', [
            'as' => 'entity.related.update',
            'uses' => 'RelatedController@update'
        ]);

        // Delete a entity relationship
        $router->delete('/{related}/{relatedId}', [
            'as' => 'entity.related.delete',
            'uses' => 'RelatedController@destroy'
        ]);
    }
);
