<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Traits\Tree;
use EasyWeChat\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderRebate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Tree;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->order->user->parent_id) return;

        $app = Factory::payment(config('wechat.payment.default'));

        $users = User::get('id','openid','name','parent_id')->toArray();
        $parents = $this->parents($users,$this->order->user->parent_id);
        //第一层
        if(isset($parents[0])){
            $app->transfer->toBalance([
                'partner_trade_no' => '1233455',
                'openid' => $parents[0]['openid'],
                'check_name' => 'FORCE_CHECK',
                're_user_name' => $parents[0]['name'],
                'amount' => 10000,
                'desc' => '理赔',
            ]);
        }

        //第二层
        if(isset($parents[0])){
            $app->transfer->toBalance([
                'partner_trade_no' => '1233455',
                'openid' => $parents[1]['openid'],
                'check_name' => 'FORCE_CHECK',
                're_user_name' => $parents[1]['name'],
                'amount' => 10000,
                'desc' => '理赔',
            ]);
        }
        //第三层
        if(isset($parents[0])){
            $app->transfer->toBalance([
                'partner_trade_no' => '1233455',
                'openid' => $parents[2]['openid'],
                'check_name' => 'FORCE_CHECK',
                're_user_name' => $parents[2]['name'],
                'amount' => 10000,
                'desc' => '理赔',
            ]);
        }

    }

}
