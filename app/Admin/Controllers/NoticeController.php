<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Notice;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;


class NoticeController extends Controller
{
    /**
     * 公告列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('公告列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Create interface.
     *
     * @param \Encore\Admin\Layout\Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('添加公告')
            ->description('description')
            ->body($this->form());
    }

    /**
     * 编辑公告
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('公告列表')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * 公告表单
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Notice::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('id');
            $form->text('content', '公告详情：')->rules('required', ['required' => '请输入公告详情']);
            $form->decimal('sort', '排序（越大越靠前）：');

            $is_display = [
                'on' => ['value' => '1', 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => '0', 'text' => '禁用', 'color' => 'danger'],
            ];

            $form->switch('status', '是否启用')->states($is_display)->default('on');
            $form->display('created_at', '添加时间');
            $form->display('updated_at', '修改时间');

            $form->saved(function (Form $form) {
                if (!$form->model()->sort) {
                    $form->model()->sort = $form->model()->id;
                    $form->model()->save();
                }
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return \Encore\Admin\Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notice());

        $grid->id('ID')->sortable();
        $grid->content('公告详情');

        $grid->status('是否启用')->display(function ($value) {
            return $value
                ? '<span class="label label-success">已启用</span>'
                : '<span class="label label-danger">未启用</span>';
        });
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });


        return $grid;
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

        return redirect(admin_url('notices'));
    }

    /**
     * 删除公告
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (Notice::destroy($id)) {
            $data = [
                'status' => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status' => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
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
        $data = $request->only(['content', 'status', 'sort']);


        if(isset($data['status'])){
            $data['status'] = is_string($data['status']) ? $data['status'] === 'on' : $data['status'];
        }
        // Update or create notice
        $banner = Notice::updateOrCreate(['id' => $id], $data);

        return $banner;
    }
}
