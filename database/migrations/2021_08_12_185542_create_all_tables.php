<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->string('email')->unique();
            $table->string('password');
        });
        Schema::create('userfavorites', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_cooperativa');
        });
        Schema::create('userappointments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_cooperativa');
            $table->dateTime('ap_datetime');
        });
        Schema::create('cooperativas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->float('stars')->default(0);
            $table->int('latitude')->nullable();
            $table->int('longitude')->nullable();
        });
        Schema::create('cooperativaphotos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cooperativa');
            $table->string('url');
        });
        Schema::create('cooperativareviews', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cooperativa');
            $table->float('rate');
        });
        Schema::create('cooperativaservices', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cooperativa');
            $table->string('name');
            $table->string('price');
        });
        Schema::create('cooperativatestimonials', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cooperativa');
            $table->string('name');
            $table->float('rate');
            $table->string('body');
        });
        Schema::create('cooperativaavailability', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cooperativa');
            $table->integer('weekday');
            $table->text('hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('userappointments');
        Schema::dropIfExists('userfavorites');
        Schema::dropIfExists('cooperativa');
        Schema::dropIfExists('cooperativaphotos');
        Schema::dropIfExists('cooperativareviews');
        Schema::dropIfExists('cooperativaservices');
        Schema::dropIfExists('cooperativatestimonials');
        Schema::dropIfExists('cooperativaavailability');
        
    }
}
