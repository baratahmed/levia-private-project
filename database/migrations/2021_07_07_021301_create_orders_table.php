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
            // $table->string('unique_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('address_id');
            $table->unsignedInteger('rest_id');

            // $table->float('total_price');
            // $table->float('discount')->default(0.0)->nullable();
            // $table->float('subtotal');
            // $table->float('vat')->default(0.0)->nullable();
            // $table->float('rest_service_charge')->default(0.0)->nullable();
            // $table->float('delivery_fee')->default(0.0)->nullable();
            // $table->float('levia_discount')->default(0.0)->nullable();
            // $table->float('total_payable');

            $table->enum('order_status', ['PLACED','CONFIRMED','FOOD_READY','FOODMAN_ASSIGNED','PICKED','DELIVERED','ACCEPTED','CANCELED'])->default('PLACED');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('food_ready_at')->nullable();
            $table->timestamp('foodman_assigned_at')->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

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
