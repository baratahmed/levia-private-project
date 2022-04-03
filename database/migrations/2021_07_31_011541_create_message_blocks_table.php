<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blocked_by'); // User/DR/Restaurant
            $table->unsignedInteger('blocked_user'); // User/DR/Restaurant
            $table->enum('blocked_by_type', ['USER','REST']);
            $table->enum('blocked_user_type', ['USER','REST']);
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
        Schema::dropIfExists('message_blocks');
    }
}
