<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRestPaymentMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rest_id');
            $table->boolean('cash')->nullable()->default(false);
            $table->boolean('visa')->nullable()->default(false);
            $table->boolean('mastercard')->nullable()->default(false);
            $table->boolean('bkash')->nullable()->default(false);
            $table->boolean('rocket')->nullable()->default(false);
            $table->boolean('nexaspay')->nullable()->default(false);
            $table->boolean('upay')->nullable()->default(false);
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
        Schema::dropIfExists('rest_payment_methods');
    }
}
