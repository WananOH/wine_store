<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->unique()->comment('订单号');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->text('address')->comment('地址');
            $table->decimal('total_amount', 10, 2)->comment('总价');
            $table->text('remark')->nullable()->comment('备注');
            $table->dateTime('paid_at')->nullable()->comment('付款时间');
            $table->string('payment_method')->nullable()->comment('付款方式');
            $table->string('payment_no')->nullable()->comment('支付单号');
            $table->boolean('closed')->default(0)->comment('订单是否关闭');
            $table->string('ship_status')->default(0)->comment('物流状态:0未付款；1已付款2未发货，2：已发货；3已签收');
            $table->text('ship_data')->nullable()->comment('物流信息');
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
        Schema::dropIfExists('orders');
    }
}
