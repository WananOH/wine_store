<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('产品标题');
            $table->unsignedInteger('category_id')->comment('产品分类id');
            $table->text('description')->comment('详细描述');
            $table->text('params')->comment('产品参数');
            $table->string('thumb')->comment('封面图');
            $table->text('image')->comment('产品图片');
            $table->unsignedInteger('stock')->default(0)->comment('当前库存');
            $table->unsignedInteger('sold_count')->default(0)->comment('销量');
            $table->decimal('price', 10, 2)->comment('单价');
            $table->tinyInteger('status')->comment('产品状态：0下架，1上架');
            $table->integer('sort')->default(0)->comment('排序，越大越靠前');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
