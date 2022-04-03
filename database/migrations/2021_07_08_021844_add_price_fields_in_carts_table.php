<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceFieldsInCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->float('total_price')->default(0.0)->nullable();
            $table->float('discount')->default(0.0)->nullable();
            $table->float('subtotal')->default(0.0)->nullable();
            $table->float('vat')->default(0.0)->nullable();
            $table->float('rest_service_charge')->default(0.0)->nullable();
            $table->float('delivery_fee')->default(0.0)->nullable();
            $table->float('levia_discount')->default(0.0)->nullable();
            $table->float('total_payable')->default(0.0)->nullable();

            $table->unsignedInteger('address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn([
                'total_price',
                'discount',
                'subtotal',
                'vat',
                'rest_service_charge',
                'delivery_fee',
                'levia_discount',
                'total_payable'
            ]);
        });
    }
}
