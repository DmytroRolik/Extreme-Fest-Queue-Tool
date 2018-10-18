<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Activities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('main_photo_url')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->integer('activity_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('activities_photos', function (Blueprint $table) {
            $table->foreign('activity_id')->references('id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        Schema::dropIfExists('activities_photos');
        Schema::dropIfExists('activities');
    }
}
