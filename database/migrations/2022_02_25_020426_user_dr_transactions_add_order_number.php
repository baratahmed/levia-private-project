<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserDrTransactionsAddOrderNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_dr_transactions', function (Blueprint $table) {
            $table->unsignedInteger('order_id')->nullable()->default(null);
            $table->enum('transaction_type', ['pick_order', 'deliver_order'])->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_dr_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'transaction_type']);
        });
    }
}
