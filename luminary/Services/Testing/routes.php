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

$router->group(
    ['namespace' => 'Luminary\Services\Testing\Controllers', 'prefix' => 'customers'],
    function ($router) {
        // Retrieve a list for customers
        $router->get('/', [
            'as' => 'customers',
            'uses' => 'CustomerController@index'
        ]);

        // Create a new entity for customers
        $router->post('/', [
            'as' => 'customers.store',
            'uses' => 'CustomerController@store'
        ]);

        // Show an entity for customers
        $router->get('/{id}', [
            'as' => 'customers.show',
            'uses' => 'CustomerController@show'
        ]);

        // Update an entity for customers
        $router->patch('/{id}', [
            'as' => 'customers.update',
            'uses' => 'CustomerController@update'
        ]);

        // Delte an entity for customers
        $router->delete('/{id}', [
            'as' => 'customers.destroy',
            'uses' => 'CustomerController@destroy'
        ]);
    }
);

// Relationship Controller
$router->group(
    ['namespace' => 'Luminary\Services\Testing\Controllers', 'prefix' => 'customers/{id}'],
    function ($router) {
        // Retrieve a customers relationship
        $router->get('/relationships/{relationship}', [
            'as' => 'customers.relationships',
            'uses' => 'CustomerRelationshipController@index'
        ]);

        // Update a customers relationship
        $router->post('/relationships/{relationship}', [
            'as' => 'customers.relationships.create',
            'uses' => 'CustomerRelationshipController@store'
        ]);

        // Update a customers relationship
        $router->patch('/relationships/{relationship}', [
            'as' => 'customers.relationships.update',
            'uses' => 'CustomerRelationshipController@update'
        ]);

        // Delete a customers relationship
        $router->delete('/relationships/{relationship}', [
            'as' => 'customers.relationships.delete',
            'uses' => 'CustomerRelationshipController@destroy'
        ]);
    }
);

// Related Controller
$router->group(
    ['namespace' => 'Luminary\Services\Testing\Controllers', 'prefix' => 'customers/{id}'],
    function ($router) {
        // Retrieve a customers relationship
        $router->get('/{related}', [
            'as' => 'customers.related',
            'uses' => 'CustomerRelatedController@index'
        ]);

        // Retrieve the customer relationship record
        $router->get('/{related}/{relatedId}', [
            'as' => 'customers.related.show',
            'uses' => 'CustomerRelatedController@show'
        ]);

        // Update a customers relationship
        $router->post('/{related}', [
            'as' => 'customers.related.create',
            'uses' => 'CustomerRelatedController@store'
        ]);

        // Update a customers relationship
        $router->patch('/{related}/{relatedId}', [
            'as' => 'customers.related.update',
            'uses' => 'CustomerRelatedController@update'
        ]);

        // Delete a customers relationship
        $router->delete('/{related}/{relatedId}', [
            'as' => 'customers.related.delete',
            'uses' => 'CustomerRelatedController@destroy'
        ]);
    }
);
