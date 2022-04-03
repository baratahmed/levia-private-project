<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestAdminsAddNameAndContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_admins', function (Blueprint $table) {
            $table->string('name', 100)->nullable();
            $table->string('contact_no', 20)->nullable();
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
            $table->dropColumn(['name', 'contact_no']);
        });
    }
}
