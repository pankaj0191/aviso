<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToProjectFinalFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_final_files', function (Blueprint $table) {
            $table->dropForeign('project_final_files_project_id_foreign');
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_final_files', function (Blueprint $table) {
            $table->dropForeign('project_final_files_project_id_foreign');
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->change();
        });
    }
}
