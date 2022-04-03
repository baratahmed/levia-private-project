<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDrTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dr_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('DR user id');
            $table->enum('with', ['user', 'rest', 'levia'])->default('user')->comment("user: take money for order, rest: give money for order, levia: deposited cash to levia");
            $table->double('amount', 8, 2)->comment('+ve: got money from user, -ve: gave money to restaurant/levia');
            $table->enum('status', ['deposited', 'undeposited', 'paid'])->default('undeposited'); // Don't know what it is
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
        Schema::dropIfExists('user_dr_transactions');
    }
}
