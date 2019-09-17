<?php

namespace App\Admin\Controllers;

use App\Models\RebateLog;
use App\Models\RewardLog;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RewardController extends AdminController
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
        $grid = new Grid(new RewardLog());

        $grid->id('ID')->sortable();
        $grid->user_id('用户姓名')->display(function ($value){
            return User::find($value)->name;
        });
        $grid->desc('描述');
        $grid->rebate_from('奖励前金额')->display(function ($value){
            return '￥' . $value . '元';
        });
        $grid->rebate_to('奖励后金额')->display(function ($value){
            return '￥' . $value . '元';
        });
        $grid->created_at('奖励日期');

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
        $show = new Show(RewardLog::findOrFail($id));

        $show->id('ID');
        $show->user_id('用户姓名')->display(function ($value){
            return User::find($value)->name;
        });
        $show->desc('描述');
        $show->rebate_from('奖励前金额')->display(function ($value){
            return '￥' . $value . '元';
        });
        $show->rebate_to('奖励后金额')->display(function ($value){
            return '￥' . $value . '元';
        });
        $show->created_at('奖励日期');

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }
}
