<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('users', 'UserController@index');

    $router->resource('categories', 'CategoryController')->names('admin.categories');
    $router->resource('products', 'ProductController')->names('admin.products');

    $router->post('orders/{order}/ship', 'OrderController@ship')->name('admin.orders.ship');
    $router->resource('orders', 'OrderController')->names('admin.orders')->only('index', 'show');
});
