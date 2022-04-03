<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSeenOnUserNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->boolean('is_seen')->default(false);
            $table->string('for_type', 20)->nullable(); // post/reservation/comment/like/offer
            $table->unsignedBigInteger('for_id')->nullable(); // post_id(post,comment,like)/reservation_id/offer_id
            $table->text('relevant_users')->nullable()->comment('JSON array of relevant users/ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn(['is_seen','for_type','for_id','relevant_users']);
        });
    }
}
