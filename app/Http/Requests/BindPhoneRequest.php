<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'phone' => ['required','regex:/^1[3|4|5|7|8|9][0-9]{9}$/',Rule::unique('users','phone')->whereNot('id',auth()->id())],
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式错误',
            'phone.unique' => '手机号已存在'
        ];
    }

    protected function failedValidation($validator)
    {
        $error = $validator->errors()->first();


        $response = response()->json([
            'status_code' => 422,
            'msg'  => $error,
            'data' => null,
        ]);

        throw new HttpResponseException($response);
    }
}
