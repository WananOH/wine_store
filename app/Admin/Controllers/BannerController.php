<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Banner列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Banner列表')
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
            ->header('创建轮播图')
            ->description('description')
            ->body($this->form());
    }

    /**
     * 编辑Banner
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Banner列表')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Banner表单
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Banner::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('id');
            $form->image('url', 'Banner：')->rules('required', ['required' => '请选择图片']);
            $form->select('product_id','要跳转的商品')->options(Product::getSelectOptions());
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
        $grid = new Grid(new Banner());

        $grid->id('ID')->sortable();
        $grid->url('banner')->image();
        $grid->product_id('链接的商品')->display(function ($value){
            return Product::find($value)->title ?? '';
        });
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

        return redirect(admin_url('banners'));
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

        return redirect(admin_url('banners'));
    }

    /**
     * 删除Banner
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (Banner::destroy($id)) {
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
     * Save Banner.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \App\Models\Product
     */
    protected function save(Request $request, $id = null)
    {
        $data = $request->only(['url','product_id', 'status', 'sort']);

        if (isset($data['url']) && $data['url'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['url']->store(
                config('admin.upload.directory.image'),
                ['disk' => config('admin.upload.disk')]
            );
            $data['url'] = Storage::disk(config('admin.upload.disk'))->url($imagePath);
        }

        if(isset($data['status'])){
            $data['status'] = is_string($data['status']) ? $data['status'] === 'on' : $data['status'];
        }
        // Update or create banner
        $banner = Banner::updateOrCreate(['id' => $id], $data);

        return $banner;
    }
}
