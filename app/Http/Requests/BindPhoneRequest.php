<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductSku;

class BindPhoneRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => ['required','regex:/^1[3|4|5|7|8|9][0-9]{9}$/'],
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式错误',
        ];
    }
}
