<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;

class CategoryController extends Controller
{
    /**
     * 商品列表
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
     * 编辑商品
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
     * 商品表单
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Category::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('id');
            $form->text('title', '商品分类名称：')->rules('required', ['required' => '请填写商品分类名称']);

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
     * Save product.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \App\Models\Product
     */
    protected function save(Request $request, $id = null)
    {
        $data = $request->only(['title', 'status']);

        if (isset($data['thumb']) && $data['thumb'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['thumb']->store(
                config('admin.upload.directory.image'),
                ['disk' => config('admin.upload.disk')]
            );
            $data['thumb'] = Storage::disk(config('admin.upload.disk'))->url($imagePath);
        }

        if (isset($data['images'])) {
            foreach ($data['images'] as $key => $image){
                $imagePath = $image->store(
                    config('admin.upload.directory.image'),
                    ['disk' => config('admin.upload.disk')]
                );
                $data['images'][$key] = Storage::disk(config('admin.upload.disk'))->url($imagePath);
            }
        }
        if(isset($data['status'])){
            $data['status'] = is_string($data['status']) ? $data['status'] === 'on' : $data['status'];
        }
        // Update or create porduct
        $product = Product::updateOrCreate(['id' => $id], $data);

        return $product;
    }
}
