<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBattlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz("battle_time");
            $table->unsignedBigInteger("event_id");
            $table->string("event_map");
            $table->string("mode");
            $table->string("type");
            $table->integer("duration");
            $table->boolean("isBalanced");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battles');
    }
}
