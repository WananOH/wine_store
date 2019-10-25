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
        $user = auth()->user();
        $order = Order::findOrFail($id);
        if($user->id != $order->user_id){
            return response()->json(['status_code' => 422,'message' => '当前订单不能删除']);
        }
        $order->closed = 1;
        $order->save();

        return response()->json(['status_code' => 204,'message' => '删除成功']);
    }

    public function confirm($id)
    {
        $user = auth()->user();
        $order = Order::findOrFail($id);
        if($order->ship_status != 2 || $user->id != $order->user_id){
            return response()->json(['status_code' => 422,'message' => '当前订单不能确认收货']);
        }
        $order->ship_status = 3;
        $order->save();

        return response()->json(['status_code' => 201,'message' => '确认收货成功']);
    }

    public function express($oreder)
    {
        $post_data = array();
        $post_data["customer"] = '3C03F5B34CC868FC685FAC94318AECF6';
        $key= 'DITWBaJI4430' ;
        $company = $oreder->ship_data['']

        $post_data["param"] = '{"com":"yuantong","num":"YT4138953865496"}';

        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\"",'"',$result );
        $data = json_decode($data,true);

        return $data;
    }
}
