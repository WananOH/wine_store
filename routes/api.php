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

    $api->group(['middleware' => ['auth:api','api_token']],function ($api) {
        $api->post('user/code','UserController@code');
        $api->put('user/phone','UserController@phone');
        $api->resource('user','UserController');
        /*用户订单*/
        $api->put('order/confirm/{id}','OrderController@confirm');
        $api->resource('order','OrderController');
        /*用户地址*/
        $api->resource('address','UserAddressController');
        /*购物车*/
        $api->get('cart', 'CartController@index');
        $api->post('cart', 'CartController@add');
        $api->delete('cart', 'CartController@remove');

        /*微信支付*/
        $api->get('wechat/pay/{id}', 'WechatController@pay');
    });

    $api->any('wechat/notify', 'WechatController@notify');

    $api->post('auth/login', 'AuthController@login');
    $api->get('category', 'CategoryController@index');
    $api->resource('product','ProductController');
    $api->get('notice','NoticeController@index');
    $api->get('banner','BannerController@index');


});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
