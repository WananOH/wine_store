<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Jobs\CloseOrder;
use App\Models\UserAddress;
use App\Services\CartService;

class OrderService
{
    /**
     * Store order.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserAddress  $address
     * @param  string  $remark
     * @param  array  $items
     * @return \App\Models\Order
     */
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
            // 更新此地址最后使用时间
            $address->update(['last_used_at' => now()]);

            $order = new Order([
                'address' => [ // 將地址信息放入订单中
                    'address' => $address->address,
                    'zip_code' => $address->zip_code,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => 0,
            ]);

            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            foreach ($items as $data) {
                $product  = Product::find($data['product_id']);
                // 创建订单与产品的关联关系
                $item = $order->items()->make([
                    'title' => $product->title,
                    'amount' => $data['amount'],
                    'price'  => $product->price,
                ]);
                $item->product()->associate($product);
                $item->save();

                $totalAmount += $product->price * $data['amount'];
                if ($product->decreaseStock($data['amount']) <= 0) {
                    throw new \Exception('该商品库存不足');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单商品从购物车移除
            $productIds = collect($items)->pluck('product_id')->all();
            app(CartService::class)->remove($productIds,0);

            return $order;
        });

        // 延迟任务，一段时间未支付，修改订单状态为关闭
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}
