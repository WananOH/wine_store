<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use EasyWeChat\Factory;

class WechatController extends Controller{

    protected $setting;

    public function __construct()
    {
        $this->setting = SettingInfoModel::first();
    }

    public function pay($id)
    {
        $user = auth()->user();
        $order = Order::findOrFail($id);
        $app = Factory::payment($this->options());

        $result = $app->order->unify([
            'body' => $order->out_trade_no,
            'out_trade_no' => $order->out_trade_no,
            'total_fee' => $order->total_amount * 100,
            'trade_type' => 'JSAPI',
            'notify_url' => url('/api/wechat/notify'),
            'openid' => $user->openid
        ]);
        \Log::info($result);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $payment = Factory::payment($this->options());
            $jssdk = $payment->jssdk;

            $json = $jssdk->bridgeConfig($result['prepay_id']);
            \Log::info($json);

            return response()->json(['status_code' => 200,'message' => '查询成功','data' => $json]);
        } else {
            $msg = "签名失败，请稍后再试!";

            return response()->json(['status_code' => 410,'message' => $msg]);
        }
    }

    public function notify()
    {
        $app = Factory::payment($this->options());
        \Log::info("wechat notify start!");
        return $app->handlePaidNotify(function ($notify, $successful) {
            \Log::info($notify);
            if ($notify['result_code'] == 'SUCCESS') {
                $order = Order::where('out_trade_no', $notify['out_trade_no'])->first();
                $order->paid_at = Carbon::now();
                $order->save();
            } else {
                return $successful('通信失败，请稍后再通知我');
            }
            return true;
        });

    }

    public function options()
    {
        return [
            'app_id' => $this->setting->app_id,
            'mch_id' => $this->setting->mch_id,
            'key' => $this->setting->api_key,
            'notify_url' => url('/api/wechat/notify'),
            'sandbox' => false,
        ];
    }
}
