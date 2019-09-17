<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\RewardLog;
use App\Models\Setting;
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
    protected $owner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->owner = User::find($order->user_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->order->user->parent_id) return;
        $setting = Setting::first();

        $users = User::get('id','openid','name','parent_id')->toArray();
        $parents = $this->parents($users,$this->order->user->parent_id);
        //第一层

        try{
            DB::beginTransaction();
            if(isset($parents[0])){
                $user = User::find($parents[0]['id']);
                $reward_first = $setting->rebate_first * $this->order->total_amount / 100;
                RewardLog::create([
                    'user_id' => $user->id,
                    'order_id' => $this->order->id,
                    'desc' => '获得'. $this->owner->name.'订单的一级分销奖励'. $reward_first .'元',
                    'rebate_from' => $user->current_rebate,
                    'rebate_to' => $user->current_rebate + $reward_first,
                ]);
                $user->current_rebate = $user->current_rebate + $reward_first;
                $user->total_rebate = $user->total_rebate + $reward_first;
                $user->save();
            }

            //第二层
            if(isset($parents[1])){
                $user = User::find($parents[1]['id']);
                $reward_second = $setting->rebate_second * $this->order->total_amount / 100;
                RewardLog::create([
                    'user_id' => $user->id,
                    'order_id' => $this->order->id,
                    'desc' => '获得'. $this->owner->name.'订单的二级分销奖励'. $reward_second .'元',
                    'rebate_from' => $user->current_rebate,
                    'rebate_to' => $user->current_rebate + $reward_second,
                ]);
                $user->current_rebate = $user->current_rebate + $reward_second;
                $user->total_rebate = $user->total_rebate + $reward_second;
                $user->save();
            }
            //第三层
            if(isset($parents[2])){
                $user = User::find($parents[2]['id']);
                $reward_third = $setting->rebate_third * $this->order->total_amount / 100;
                RewardLog::create([
                    'user_id' => $user->id,
                    'order_id' => $this->order->id,
                    'desc' => '获得'. $this->owner->name.'订单的三级分销奖励'. $reward_third .'元',
                    'rebate_from' => $user->current_rebate,
                    'rebate_to' => $user->current_rebate + $reward_third,
                ]);
                $user->current_rebate = $user->current_rebate + $reward_third;
                $user->total_rebate = $user->total_rebate + $reward_third;
                $user->save();
            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
        }
    }

}
