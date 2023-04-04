<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProfileDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_descriptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->integer('profile_id')->after('body')->nullable()->index();
            $table->decimal('hourly_rate', 8, 2)->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
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
            $table->integer('user_id')->after('body')->nullable()->index();
            $table->dropColumn('hourly_rate', 'profile_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->default(0);
        });
    }
}
