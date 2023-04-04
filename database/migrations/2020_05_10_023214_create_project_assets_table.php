<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_assets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('project_id')->unsigned()->index();
            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->longText('url');

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
        Schema::dropIfExists('project_assets');
    }
}
