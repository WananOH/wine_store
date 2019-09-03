<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class OrderController extends Controller{
    public function index()
    {
        $user = auth()->user();
        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->load(['items.product']);

        return response()->json(['status_code' => 200,'message' => '查询成功','data' => $order]);
    }

    public function store(OrderRequest $request)
    {
        $user = auth()->user();
        $address = UserAddress::find($request->input('address_id'));



    }
}
