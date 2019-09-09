<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Product;
use App\Models\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;

class UserController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('用户列表')
            ->description('description')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new User());

        $grid->id('ID')->sortable();
        $grid->name('用户姓名');
        $grid->avatar('用户头像')->image();
        $grid->phone('用户电话');
        $grid->created_at('注册时间');

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });


        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('用户详情')
            ->description('description')
            ->body($this->detail($id));
    }

    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('ID');
        $show->name('用户姓名');
        $show->nickname('用户昵称');
        $show->avatar('用户头像')->image();
        $show->phone('联系电话');
        $show->created_at('注册日期');

        $show->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }
}
