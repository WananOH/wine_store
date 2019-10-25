<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BindPhoneRequest;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Overtrue\EasySms\EasySms;

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
        $code = mt_rand(100000,999999);
        Redis::setex($request->phone,120,$code);

        $easySms = new EasySms(config('app.aliyun_sms'));
        $easySms->send($request->phone, [
            'content'  => '您的验证码为: ' . $code,
            'template' => 'SMS_173625597',
            'data' => [
                'code' => $code
            ],
        ]);

        return response()->json(['status_code' => 201,'message' => '发送成功','data' => auth()->id()]);
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

    public function rebate()
    {
        $user = auth()->user();
        $app = Factory::payment(config('wechat.payment.default'));

        $redpack = $app->redpack;
        $redpackData = [
            'mch_billno'   => $this->getTradeNo(),
            'send_name'    => '西贝莱斯',
            're_openid'    => $user->openid,
            'total_amount' => $user->current_rebate * 100,  //单位为分，不小于100
            'wishing'      => '西贝莱斯分销奖励',
            'act_name'     => '西贝莱斯分销奖励',
            'remark'       => '西贝莱斯分销奖励',
            // ...
        ];

        $response  = $redpack->sendNormal($redpackData);

        /*$response = $app->transfer->toBalance([
            'partner_trade_no' => $this->getTradeNo(), // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $user->openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name' => $user->name, // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount' => $user->current_rebate * 100, // 企业付款金额，单位为分
            'desc' => '分销奖励提现', // 企业付款操作说明信息。必填
        ]);*/

        return $response;
    }

    public  function getTradeNo()
    {
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            return $no;
        }

        return false;
    }

    public function address(Request $request){
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $apis = ['openAddress'];
        $url = $request->get('url') ?? env('H5_URL');
        $app->jssdk->setUrl($url);
        $json = $app->jssdk->buildConfig($apis, false);

        return response()->json(['status_code' => 200,'message' => '获取成功','data' => $json]);
    }

}
