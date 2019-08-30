<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

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

    }
}
