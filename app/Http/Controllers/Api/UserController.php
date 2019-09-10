<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BindPhoneRequest;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller{

    public function index()
    {
        $user = auth()->user();
        $user = collect($user)->only('name','nickname','avatar','phone','email','qrcode');

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $user]);
    }

    public function code(BindPhoneRequest $request)
    {
        $user = auth()->user();
        $code = mt_rand(000000,999999);
        $code = Redis::setex($request->phone,60,$code);
        //todo 发送短信
        return response()->json(['status_code' => 201,'message' => '发送成功','data' => $code]);
    }

    /**
     * 绑定手机号
     * @param BindPhoneRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phone(BindPhoneRequest $request)
    {
        $user = auth()->user();
        $code = Redis::get($request->phone);

        if(!request('code') || $code != request('code'))  return response()->json(['status_code' => 422,'message' => '验证码错误,请重新输入']);

        $user->phone = $request->phone;
        $user->save();
        return response()->json(['status_code' => 201,'message' => '绑定成功']);
    }

}
