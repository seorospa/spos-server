<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return response()->json([
        'app' => 'spos',
        'version' => '0.1',
        'contributors' => [
            'Sebastián Osorio <seorospa@gmail.com>',
            'César Bravo <cbravo@bcyt.cl>',
            'Matías Gómez <matias.gomez@virginiogomez.cl>'
        ]
    ]);
});

$cruld = [
    'users' => 'UserController',
    'tickets' => 'TicketController',
    'taxes' => 'TaxController',
    'products' => 'ProductController',
    'categories' => 'CategoryController',
    'clients' => 'ClientController',
    'transactions' => 'TransactionController',
];

foreach ($cruld as $key => $ctl) {
    $router->group(['prefix' => $key], function () use ($router, $ctl) {
        $router->get('/', $ctl . '@list');
        $router->post('/', $ctl . '@create');
        $router->get('{id}', $ctl . '@read');
        $router->put('{id}', $ctl . '@update');
        $router->delete('{id}', $ctl . '@delete');
    });
}

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/', 'AuthController@login');
    $router->get('/', 'AuthController@me');
    $router->put('/', 'AuthController@refresh');
});

$router->put('/products/{id}/stock', 'ProductController@stock');

$router->group(['prefix' => 'tickets'], function () use ($router) {
    $router->delete('/{id}/cart', 'TicketController@deleteProduct');
    $router->put('/{id}/cart', 'TicketController@changeProductQty');
    $router->put('/{id}/claim', 'TicketController@claim');
});
