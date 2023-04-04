<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStorageCapacityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_descriptions', function (Blueprint $table) {
            $table->integer('storage_capacity')->after('profile_id')->default(1);
        });

        Schema::table('comment_files', function (Blueprint $table) {
            $table->bigInteger("size")->after('thumb_path')->default(0);
        });

        Schema::table('issue_files', function (Blueprint $table) {
            $table->bigInteger("size")->after('thumb_path')->default(0);
        });

        Schema::table('project_assets', function (Blueprint $table) {
            $table->bigInteger("size")->after('thumb_path')->default(0);
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->bigInteger("size")->after('thumb_path')->default(0);
        });

        Schema::table('project_final_files', function (Blueprint $table) {
            $table->bigInteger("size")->after('path')->default(0);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->integer("storage_capacity")->after("price")->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_descriptions', function (Blueprint $table) {
            $table->dropColumn('storage_capacity');
        });

        Schema::table('comment_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('issue_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('project_assets', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('project_final_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn("storage_capacity");
        });
    }
}
