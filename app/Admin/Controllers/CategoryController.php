<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;

class CategoryController extends Controller
{
    /**
     * 分类列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品分类列表')
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
            ->header('添加分类')
            ->description('description')
            ->body($this->form());
    }

    /**
     * 编辑分类
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品分类')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * 分类表单
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Category::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('id');
            $form->text('title', '商品分类名称：')->rules('required', ['required' => '请填写商品分类名称']);
            $form->decimal('sort', '排序（越大越靠前）：');

            $is_display = [
                'on' => ['value' => '1', 'text' => '启用', 'color' => 'primary'],
                'off' => ['value' => '0', 'text' => '禁用', 'color' => 'danger'],
            ];

            $form->switch('status', '是否启用')->states($is_display)->default('on');
            $form->display('created_at', '添加时间');
            $form->display('updated_at', '修改时间');
            $form->saving(function (Form $form) {
                if (!$form->id) {
                    $exist = Category::where(['title' => $form->title])
                        ->first();
                    if ($exist) {
                        $error = new MessageBag([
                            'title' => '错误信息',
                            'message' => '商品分类已经存在啦！',
                        ]);
                        return back()->with(compact('error'))->withInput();
                    }
                }


            });
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
        $grid = new Grid(new Category());

        $grid->id('ID')->sortable();
        $grid->title('商品分类名称');
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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->save($request);

        admin_toastr(trans('admin.save_succeeded'));

        return redirect(admin_url('categories'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->save($request, $id);

        admin_toastr(trans('admin.update_succeeded'));

        return redirect(admin_url('categories'));
    }

    /**
     * 删除商品分类
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (Category::destroy($id)) {
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
     * Save category.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \App\Models\Category
     */
    protected function save(Request $request, $id = null)
    {
        $data = $request->only(['title', 'status','sort']);

        if(isset($data['status'])){
            $data['status'] = is_string($data['status']) ? $data['status'] === 'on' : $data['status'];
        }
        // Update or create porduct
        $product = Category::updateOrCreate(['id' => $id], $data);

        return $product;
    }
}
