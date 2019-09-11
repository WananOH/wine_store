<?php

namespace App\Http\Controllers\WeChat;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

class WeChatController extends Controller
{
    protected $app;


    public function __construct()
    {
        $this->app = Factory::officialAccount(config('wechat.official_account.default'));
    }


    public function server()
    {
        $response = $this->app->server->serve();

        return $response;
    }

    public function menu()
    {
        $list = $this->app->menu->current();
        return $list;
    }

}
