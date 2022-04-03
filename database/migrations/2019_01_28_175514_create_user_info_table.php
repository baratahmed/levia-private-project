<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fb_user_no');
            $table->unsignedInteger('role_id')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->string('fb_profile_name');
            $table->string('fb_profile_pic_url')->comment('file:FILENAME or default.png or https://graph.facebook.com URL');
            $table->string('contact_no', 18)->unique();
            $table->string('user_email')->nullable();
            $table->text('user_bio')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('user_info');
    }
}
