<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestAdminsProfileCompletenessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_admins_profile_completeness', function (Blueprint $table) {
            $table->unsignedInteger('radmin_id');
            $table->boolean('is_registered')->default(true);
            $table->boolean('is_restaurant_added')->default(false)->nullable();
            $table->boolean('is_name_added')->default(false)->nullable();

            $table->unique(['radmin_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rest_admins_profile_completeness');
    }
}
