<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('conversation_id');
            $table->unsignedInteger('from_id');
            $table->unsignedInteger('to_id');
            $table->enum('from_user_type', ['USER','REST']);
            $table->enum('to_user_type', ['USER','REST']);
            $table->text('message')->nullable();
            $table->text('attachments')->nullable()->comment('JSON Column: [{ "src" : "/path/to/image.png" }]');
            $table->text('gen_html')->nullable();
            $table->enum('seen_status', ['UNSEEN','NOTIFIED','SEEN'])->default('UNSEEN');
            $table->boolean('deleted_from')->default(false);
            $table->boolean('deleted_to')->default(false);
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
        Schema::dropIfExists('messages');
    }
}
