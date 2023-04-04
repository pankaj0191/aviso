<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToCategoryUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_user', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->integer('profile_id')->first('category_id')->unsigned()->index()->nullable();
        });

        Schema::rename('category_user', 'category_profile');

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->integer('profile_id')->after('dpi')->unsigned()->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('category_profile', 'category_user');
        Schema::table('category_user', function (Blueprint $table) {
            $table->integer('user_id')->first('category_id')->unsigned()->index()->nullable();
            $table->dropColumn('profile_id');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->integer('user_id')->after('dpi')->unsigned()->index()->nullable();
            $table->dropColumn('profile_id');
        });
    }
}
