<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\OrderRebate;
use App\Models\Order;
use Carbon\Carbon;
use EasyWeChat\Factory;

class WechatController extends Controller{

    public function pay($id)
    {
        $user = auth()->user();
        $order = Order::findOrFail($id);
        $app = Factory::payment(config('wechat.payment.default'));

        $result = $app->order->unify([
            'body' => $order->no,
            'out_trade_no' => $order->no,
            'total_fee' => $order->total_amount * 100,
            'trade_type' => 'JSAPI',
            'notify_url' => url('/api/wechat/notify'),
            'openid' => $user->openid
        ]);
        \Log::info($result);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $json = $app->jssdk->bridgeConfig($result['prepay_id']);
            \Log::info($json);

            return response()->json(['status_code' => 200,'message' => '获取成功','data' => $json]);
        } else {
            $msg = "签名失败，请稍后再试!";

            return response()->json(['status_code' => 410,'message' => $msg]);
        }
    }

    public function notify()
    {
        $app = Factory::payment(config('wechat.payment.default'));
        \Log::info("wechat notify start!");
        return $app->handlePaidNotify(function ($notify, $successful) {
            \Log::info($notify);
            if ($notify['result_code'] == 'SUCCESS') {
                $order = Order::where('no', $notify['out_trade_no'])->first();
                $order->paid_at = Carbon::now();
                $order->payment_method = 'wechat';
                $order->payment_no = $notify['transaction_id'];
                $order->ship_status = 1;
                $order->save();
               // $this->dispatch(new OrderRebate($order));
            } else {
                return $successful('通信失败，请稍后再通知我');
            }
            return true;
        });

    }
}
