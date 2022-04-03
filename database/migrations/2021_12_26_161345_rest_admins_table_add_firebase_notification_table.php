<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestAdminsTableAddFirebaseNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_admins', function (Blueprint $table) {
            $table->string('firebase_notification_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rest_admins', function (Blueprint $table) {
            $table->dropColumn(['firebase_notification_token']);
        });
    }
}
