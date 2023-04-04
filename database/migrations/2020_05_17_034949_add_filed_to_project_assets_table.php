<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiledToProjectAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_assets', function (Blueprint $table) {
            $table->text('thumb_path')->after('url')->nullable();
            $table->enum('type', ['assets', 'photos'])->default('assets')->after('thumb_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_assets', function (Blueprint $table) {
            $table->dropColumn('type', 'thumb_path');
        });
    }
}
