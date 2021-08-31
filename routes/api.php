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

$router->group(['prefix' => 'tickets/{id}'], function () use ($router) {
    $router->delete('/cart', 'TicketController@deleteProduct');
    $router->put('/cart', 'TicketController@changeProductQty');
    $router->post('/cart/common', 'TicketController@changeCommonProduct');
    $router->put('/claim', 'TicketController@claim');
});

$router->group(['prefix' => 'summary'], function () use ($router) {
    $router->get('', 'SummaryController@simple');
    $router->get('/latest', 'SummaryController@latestSales');
    $router->get('/stockAlerts', 'SummaryController@stockAlerts');
});
