<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param Order $order
     * @param Content $content
     * @return Content
     */
    public function show(Order $order, Content $content)
    {
        $express = Order::express();
        return $content
            ->header('查看订单')
            ->description('description')
            ->body(view('admin.orders.show', ['order' => $order,'express' => $express]));
    }

    public function grid()
    {
        $grid = new Grid(new Order());

        $grid->id('ID')->sortable();
        $grid->no('订单号')->sortable();
        $grid->total_amount('订单总价')->sortable();
        $grid->user_id('买家姓名')->display(function ($value){
            return User::find($value)->name;
        });
        $grid->paid_at('是否付款')->display(function ($value){
            return $value ? '已付款' : '未付款';
        });
        $grid->payment_method('付款方式');
        $grid->ship_status('订单状态')->using([
            0 => '未支付',
            1 => '未发货',
            2 => '已发货',
            3 => '已签收',
        ])->label([
            0 => 'default',
            1 => 'warning',
            2 => 'info',
            3 => 'success',
        ]);
        $grid->updated_at('更新时间');
        $grid->model()->where ('closed', 0)->orderBy('created_at','desc');
        $grid->filter(function($filter){

            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, '买家姓名');

            $filter->equal('ship_status')->select([
                0 => '未支付',
                1 => '未发货',
                2 => '已发货',
                3 => '已签收',
            ]);

        });
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        return $grid;
    }

    /**
     * 发货
     *
     * @param Order $order
     * @param Request $request
     * @return void
     */
    public function ship(Order $order, Request $request)
    {
        if (!$order->paid_at) {
            admin_toastr('该订单尚未付款');
        }
        if ($order->ship_status > 1) {
            admin_toastr('该订单已发货');
        }

        $data = $request->validate([
            'express_company' => ['required'],
            'express_no' => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);

        $order->update([
            'ship_status' => 2,
            'ship_data' => $data,
        ]);

        return redirect()->back();
    }
}
