<?php

namespace App\Jobs;

use App\Models\Order;
use EasyWeChat\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderRebate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        //第一层
        $app->transfer->toBalance([
            'partner_trade_no' => '1233455',
            'openid' => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
            'check_name' => 'FORCE_CHECK',
            're_user_name' => '王小帅',
            'amount' => 10000,
            'desc' => '理赔',
        ]);

        //第二层

        //第三层
    }
}
