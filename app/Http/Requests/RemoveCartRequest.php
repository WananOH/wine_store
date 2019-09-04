<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductSku;

class RemoveCartRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => [
                'required'
            ],
        ];
    }

}
