<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_timers', function (Blueprint $table) {
            $table->increments('id');
            //User ID
            $table->integer('user_id')->unsigned()->index();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            //Project ID
            $table->integer('project_id')->unsigned()->index();
            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            // Time properties
            $table->text('description')->nullable();
            $table->boolean('is_locked')->default(0);
            
            // Time rate
            $table->decimal('amount', 8, 2)->default(0);
            $table->decimal('hourly_rate', 8, 2)->default(0);

            // Time Interval
            $table->integer('duration')->nullable();
            $table->datetime('start');
            $table->datetime('end')->nullable();
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
        Schema::dropIfExists('project_timers');
    }
}
