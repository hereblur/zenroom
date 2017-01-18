<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Rooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roomtypes', function (Blueprint $table) {
           $table->increments('id');
           $table->string('type')->unique();
           $table->integer('inventory');
           $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
           $table->increments('id');
           $table->date('date');
           $table->integer('roomtypes');
           $table->integer('availability');
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
        //
    }
}
