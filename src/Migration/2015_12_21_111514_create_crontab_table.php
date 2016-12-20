<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrontabTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crontab', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 40);
            $table->boolean('enabled',1);
            $table->string('schedule');
            $table->string('class');
            $table->integer('last');
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
        Schema::dropIfExists('crontab');
    }
}
