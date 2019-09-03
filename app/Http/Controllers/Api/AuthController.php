<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller{

    public function login(Request $request)
    {
        $user = User::first();
        if (\Auth::loginUsingId($user->id)) {
            $user = \Auth::user();
            $token = $user->createToken($user->id . '-' . $user->openid)->accessToken;

            return response()->json(['status_code' => 200,'message' => '查询成功','data' => $token]);
        } else {
            return response()->json(['status_code' => 404,'message' => '用户不存在']);
        }
    }


}
