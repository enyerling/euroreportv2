<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('record_evaluation_id')->unsigned();
            $table->integer('system_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->string('answer')->nullable();
            $table->date('date')->nullable();
            $table->integer('room')->nullable();
            $table->integer('instance')->nullable();
            $table->timestamps();
            $table->foreign('record_evaluation_id')->references('id')->on('record_evaluations')->onDelete('cascade');
            $table->foreign('system_id')->references('id')->on('systems')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
}
