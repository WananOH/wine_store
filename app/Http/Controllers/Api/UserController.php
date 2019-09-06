<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserController extends Controller{

    public function index()
    {
        $user = auth()->user();
        $user = collect($user)->only('name','nickname','avatar','phone','email','qrcode');

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $user]);
    }

}
