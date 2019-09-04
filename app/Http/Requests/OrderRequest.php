<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;

class OrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id'     => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
            'items'          => 'required|array',
            'items.*.product_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$product = Product::find($value)) {
                        $fail('该商品不存在');
                        return;
                    }
                    if (!$product->status) {
                        $fail('该商品未上架');
                        return;
                    }
                    if ($product->stock === 0) {
                        $fail('该商品已售完');
                        return;
                    }

                    preg_match('/items\.(\d+)\.product_id/', $attribute, $m);
                    $index  = $m[1];
                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $product->stock) {
                        return $fail('该商品库存不足');
                    }
                },
            ],
            'items.*.amount' => 'required|integer|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'items' => '商品',
            'items.*.product_id' => '商品',
            'items.*.amount' => '商品数量',
        ];
    }
}
