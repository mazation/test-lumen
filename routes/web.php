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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', ['uses' => 'UserController@create']);
    $router->post('login', ['uses' => 'UserController@login']);
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('users/events', ['uses' => "UserController@addEvent"]);
        $router->get('users/{id}', ['uses' => 'UserController@get']);
        $router->post('users/{id}', ['uses' => 'UserController@update']);

        $router->post('events', ['uses' => "EventsController@create"]);

        $router->get('events/{id}', ['uses' => "EventsController@get"]);
        $router->post('events/{id}', ['uses' => "EventsController@update"]);

        $router->delete('events/{id}', ['uses' => "EventsController@delete"]);
        $router->get('events', ['uses' => "EventsController@list"]);
        $router->post('events/{id}/pictures', ['uses' => 'EventsController@addPicture']);
        $router->get('events/{id}/pictures', ['uses' => 'EventsController@downloadPicture']);
    });
});
