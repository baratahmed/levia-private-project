<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestFacilityAddMoreProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_facility', function (Blueprint $table) {
            $table->boolean('catering')->nullable();
            $table->boolean('delivery')->nullable();
            $table->boolean('good_for_groups')->nullable();
            $table->boolean('good_for_kids')->nullable();
            $table->boolean('takes_reservations')->nullable();
            $table->boolean('take_out')->nullable();
            $table->boolean('waiter_service')->nullable();
            $table->boolean('walk_ins_welcome')->nullable();
            $table->boolean('parking_lot')->nullable();
            $table->boolean('soft_music')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rest_facility', function (Blueprint $table) {
            $table->dropColumn([
                'catering',
                'delivery',
                'good_for_groups',
                'good_for_kids',
                'takes_reservations',
                'take_out',
                'waiter_service',
                'walk_ins_welcome',
                'parking_lot',
                'soft_music',
            ]);
        });
    }
}
