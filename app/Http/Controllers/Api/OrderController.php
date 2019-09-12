<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        $status = request('ship_status');

        $query = Order::with(['items.product'])->where('user_id', $user->id)
            ->where('closed','!=',1);
            if( $status != 4){
                $query->where('ship_status',$status);
            };

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(4);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $orders]);
    }

    /**
     * 订单详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->load(['items.product']);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $order]);
    }

    /**
     * 创建订单
     * @param OrderRequest $request
     * @param OrderService $orderService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request,OrderService $orderService)
    {
        $user = auth()->user();
        $address = UserAddress::findOrFail($request->input('address_id'));

        $orderService->store($user, $address, $request->input('remark'), $request->input('items'));

        return response()->json(['status_code' => 201,'message' => '添加成功']);
    }

    /**
     * 删除订单
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->closed = 1;
        $order->save();

        return response()->json(['status_code' => 204,'message' => '删除成功']);
    }
}
