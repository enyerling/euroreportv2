<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type', 30);
            $table->string('answer')->nullable()->nullable();
            $table->integer('system_id')->unsigned();
            $table->integer('accessorie_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('system_id')->references('id')->on('systems')->onDelete('cascade');
            $table->foreign('accessorie_id')->references('id')->on('accessories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
