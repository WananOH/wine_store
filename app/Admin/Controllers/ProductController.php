<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class ProductController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param \Encore\Admin\Layout\Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param int $id
     * @param \Encore\Admin\Layout\Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('商品')
            ->description('description')
            ->body($this->detail($id));
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
            ->header('添加商品')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Edit interface.
     *
     * @param int $id
     * @param \Encore\Admin\Layout\Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
            ->description('description')
            ->body($this->form()->edit($id));
    }


    /**
     * Make a grid builder.
     *
     * @return \Encore\Admin\Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->thumb('封面图片')->image();
        $grid->title('商品名称');
        $grid->status('是否上架')->display(function ($value) {
            return $value
                ? '<span class="label label-success">已上架</span>'
                : '<span class="label label-danger">未上架</span>';
        });
        $grid->price('价格')->display(function ($price) {
            return '￥' . $price;
        });
        $grid->stock('库存');
        $grid->sold_count('销量');


        $grid->actions(function ($actions) {
            $actions->disableView();
//            $actions->disableDelete();
        });

//        $grid->tools(function ($tools) {
//            $tools->batch(function ($batch) {
//                $batch->disableDelete();
//            });
//        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param int $id
     * @return \Encore\Admin\Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('ID');
        $show->title('商品名称');
        $show->thumb('封面图片')->image();
        $show->description('商品介紹')->unescape();
        $show->status('是否上架')->unescape()->as(function ($value) {
            return $value
                ? '<span class="label label-success">已上架</span>'
                : '<span class="label label-danger">未上架</span>';
        });
        $show->price('价格')->as(function ($price) {
            return '￥' . $price;
        });
        $show->stock('库存');
        $show->sold_count('销量');
        $show->created_at('添加日期');

        return $show;
    }


    /**
     * @param null $id
     * @return Form
     */
    protected function form($id = null)
    {
        return Admin::form(Product::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('id');
            $form->text('title', '产品名称：')->rules('required', ['required' => '请填写资讯标题']);
            $category = Category::getSelectOptions();

            $form->select('category_id', '产品分类')->options($category);

            $form->image('thumb', '封面图：')->help('请上传jpeg,jpg,png格式的广告图片')->uniqueName()->removable();
            $form->multipleImage('images', '产品图片')->help('按住ctrl可以选择多张图片')->removable();
            $form->UEditor('description', '产品详情')->rules('required', ['required' => '请填写资讯标题']);
            $form->table('params','产品参数', function ($table) {
                $table->text('key','参数名称');
                $table->text('value','参数值');
            });

            $form->decimal('price', '产品价格')->rules('required', ['required' => '请填写产品价格']);
            $form->number('stock', '库存')->rules('required|integer', ['required' => '库存不能为空']);
            $is_display = [
                'on' => ['value' => '1', 'text' => '上架', 'color' => 'primary'],
                'off' => ['value' => '0', 'text' => '下架', 'color' => 'danger'],
            ];

            $form->switch('status', '是否上架')->states($is_display)->default('on');
            $form->display('created_at', '添加时间');
            $form->display('updated_at', '修改时间');
            $form->saving(function (Form $form) {
                if (!$form->id) {
                    $exist = Product::where(['title' => $form->title])
                        ->first();
                    if ($exist) {
                        $error = new MessageBag([
                            'title' => '错误信息',
                            'message' => '产品名称已经存在啦！',
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


}
