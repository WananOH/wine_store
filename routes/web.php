<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('wechat', 'WeChat\WeChatController@server');
Route::any('wechat/menu', 'WeChat\WeChatController@menu');

Route::group(['middleware' => ['wechat.oauth']], function () {
    Route::get('/wechat/auth','WeChat\WeChatController@auth');
});
