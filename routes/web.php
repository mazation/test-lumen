<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function() use ($router) {
    $router->post('register', ['uses' => 'UserController@create']);
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('events/{id}', ['uses' => "EventsController@get"]);
        $router->post('events', ['uses' => "EventsController@create"]);
        $router->post('events/{id}', ['uses' => "EventsController@update"]);
        $router->delete('events/{id}', ['uses' => "EventsController@delete"]);
        $router->get('events', ['uses' => "EventsController@list"]);
    });
});

