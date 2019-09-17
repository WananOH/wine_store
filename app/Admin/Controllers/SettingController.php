<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Notice;
use App\Models\Setting;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    /**
     * 公告列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $id = Setting::first()->id ?? 0;
        if($id) {
            return $content
                ->header('分销提成比例设置')
                ->description('description')
                ->body($this->form()->edit($id));
        }else{
            return $content
                ->header('分销提成比例设置')
                ->description('description')
                ->body($this->form());
        }
    }

    /**
     * 公告表单
     * @return Form
     */
    protected function form()
    {

        return Admin::form(Setting::class, function (Form $form) {
            $form->decimal('rebate_first','一级分销提出比例(%)');
            $form->decimal('rebate_second','二级分销提出比例(%)');
            $form->decimal('rebate_third','三级分销提出比例(%)');
            $form->setAction('settings');

            $form->footer(function ($footer) {
                $footer->disableReset();
                $footer->disableViewCheck();
                $footer->disableEditingCheck();
                $footer->disableCreatingCheck();
            });

        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->save($request);

        admin_toastr(trans('admin.save_succeeded'));

        return redirect(admin_url('settings'));
    }


    /**
     * Save notice.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \App\Models\Product
     */
    protected function save(Request $request, $id = null)
    {
        $data = $request->only(['rebate_first', 'rebate_second', 'rebate_third']);

        // Update or create notice
        $setting = Setting::updateOrCreate(['id' => $id], $data);

        return $setting;
    }
}
