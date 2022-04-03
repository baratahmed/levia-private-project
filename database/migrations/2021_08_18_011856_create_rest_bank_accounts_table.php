<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rest_id');
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('branch_name');
            $table->string('account_number');
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
        Schema::dropIfExists('rest_bank_accounts');
    }
}
