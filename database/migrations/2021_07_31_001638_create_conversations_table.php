<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id_1'); // User/DR/Restaurant
            $table->unsignedInteger('user_id_2'); // User/DR/Restaurant
            $table->enum('user_id_1_type', ['USER','REST']);
            $table->enum('user_id_2_type', ['USER','REST']);
            $table->unsignedInteger('order_id')->nullable();
            $table->enum('type', ['USER_TO_USER','ORDER_RELATED'])->default('ORDER_RELATED');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('conversations');
    }
}
