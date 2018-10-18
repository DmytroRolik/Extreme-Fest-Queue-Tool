<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersToActivitiesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_to_activities_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('schedule_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('users_to_activities_permissions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('schedule_id')->references('id')->on('schedule');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_to_activities_permissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('users_to_activities_permissions', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
        });

        Schema::dropIfExists('users_to_activities_permissions');
    }
}
