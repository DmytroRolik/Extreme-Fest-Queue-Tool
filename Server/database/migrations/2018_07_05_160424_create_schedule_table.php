<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('activity_id')->unsigned();
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date');
            $table->boolean('queue')->default(false);
            $table->boolean('is_working');
            $table->integer('sort_position');
        });

        Schema::table('schedule', function (Blueprint $table){

            $table->foreign('activity_id')->references('id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule');
    }
}
