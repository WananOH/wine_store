<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddressRequest;
use Illuminate\Http\Request;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    /**
     * The user address fields.
     *
     * @var array
     */
    private $field = [
        'province',
        'city',
        'district',
        'address',
        'zip_code',
        'contact_name',
        'contact_phone',
    ];

    /**
     * 用户地址列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $address = $request->user()->addresses;

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $address]);
    }

    /**
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only($this->field));

        return response()->json(['status_code' => 201,'message' => '创建成功']);
    }

    /**
     * @param $id
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id,UserAddressRequest $request)
    {
        $user_address = UserAddress::findorFail($id);
        $user_address->update($request->only($this->field));

        return response()->json(['status_code' => 201,'message' => '更新成功']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user_address = UserAddress::findorFail($id);

        $user_address->delete();
        return response()->json(['status_code' => 204,'message' => '删除成功']);
    }

    public function show($id)
    {
        $user_address = UserAddress::findOrFail($id);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $user_address]);
    }
}
