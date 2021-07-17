<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    $arr = [
        'app' => 'spos',
        'version' => '0.1',
        'contributors' => [
            'Sebastián Osorio <seorospa@gmail.com>',
            'César Bravo <cbravo@bcyt.cl>',
            'Matías Gómez <matias.gomez@virginiogomez.cl>'
        ]
    ];
    return response()->json($arr);
});

$router->post('auth/{id}', 'UserController@login');
$router->get('users', 'UserController@list');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('auth', 'UserController@refresh');

    $router->group(['prefix' => 'tickets'], function () use ($router) {
        $router->get('/', 'TicketController@list');
        $router->post('/', 'TicketController@create');
        $router->get('{id}', 'TicketController@read');
        $router->put('{id}', 'TicketController@update');
        $router->delete('{id}', 'TicketController@delete');
    });

    $router->group(['prefix' => 'products'], function () use ($router) {
        $router->get('/', 'ProductController@list');
        $router->post('/', 'ProductController@create');
        $router->post('/bulk', 'ProductController@create_bulk');
        $router->get('{id}', 'ProductController@read');
        $router->put('{id}', 'ProductController@update');
        $router->delete('{id}', 'ProductController@delete');
    });

    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->post('/', 'UserController@create');
        $router->get('{id}', 'UserController@read');
        $router->put('{id}', 'UserController@update');
        $router->delete('{id}', 'UserController@delete');
    });

    $router->group(['prefix' => 'clients'], function () use ($router) {
        $router->get('/', 'ClientController@list');
        $router->post('/', 'ClientController@create');
        $router->get('{id}', 'ClientController@read');
        $router->put('{id}', 'ClientController@update');
        $router->delete('{id}', 'ClientController@delete');
    });

    $router->group(['prefix' => 'categories'], function () use ($router) {
        $router->get('', 'CategoryController@list');
        $router->post('', 'CategoryController@create');
        $router->get('{id}', 'CategoryController@read');
        $router->put('{id}', 'CategoryController@update');
        $router->delete('{id}', 'CategoryController@delete');
    });
});
