<?php

namespace App\Admin\Controllers;

use App\Models\RebateLog;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RebateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Example controller';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RebateLog());

        $grid->id('ID')->sortable();
        $grid->user_id('用户姓名')->display(function ($value){
            return User::find($value)->name;
        });
        $grid->partner_trade_no('商户流水号');
        $grid->amount('提现金额')->display(function ($value){
            return '￥' . $value . '元';
        });

        $grid->created_at('提现日期');

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->filter(function($filter){

            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, '用户姓名');

        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(RebateLog::findOrFail($id));

        $show->id('ID');
        $show->user_id('用户姓名')->display(function ($value){
            return User::find($value)->name;
        });
        $show->partner_trade_no('商户流水号');
        $show->amount('提现金额')->display(function ($value){
            return '￥' . $value . '元';
        });
        $show->created_at('提现日期');

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }
}
