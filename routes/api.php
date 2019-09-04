<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api'], function ($api) {

    $api->group(['middleware' => 'auth:api'],function ($api) {
        $api->resource('order','OrderController');

        /*购物车*/
        $api->get('cart', 'CartController@index');
        $api->post('cart', 'CartController@add');
        $api->delete('cart', 'CartController@remove');

    });

    $api->post('auth/login', 'AuthController@login');
    $api->get('category', 'CategoryController@index');
    $api->resource('product','ProductController');


});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
