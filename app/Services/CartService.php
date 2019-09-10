<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get cart items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return Auth::user()->cartItems()->with(['product'])->get();
    }

    /**
     * Add product sku to cart.
     *
     * @param  int  $sku_id
     * @param  int  $amount
     * @return \App\Models\CartItem
     */
    public function add($product_id, $amount)
    {
        $user = auth()->user();
        // 查询商品是否在购物车
        if ($item = $user->cartItems()->where('product_id', $product_id)->first()) {
            // 如果存在则叠加商品数量
            $item->increment('amount', $amount);
        } else {
            // 否则创建新的购物车记录
            $item = new Cart(['amount' => $amount]);
            $item->user()->associate($user);
            $item->product()->associate($product_id);
            $item->save();
        }

        return $item;
    }

    /**
     * @param $product_ids
     */
    public function remove($product_ids)
    {
        $product_ids = is_array($product_ids) ? $product_ids : func_get_args();

        Auth::user()->cartItems()->whereIn('id', $product_ids)->delete();
    }
}
