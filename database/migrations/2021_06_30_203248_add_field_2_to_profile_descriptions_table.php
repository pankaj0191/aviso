<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddField2ToProfileDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_descriptions', function (Blueprint $table) {
            $table->text('dark_logo')->after('body')->nullable();
            $table->text('white_logo')->after('dark_logo')->nullable();
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
            $table->dropColumn('dark_logo', 'white_logo');
        });
    }
}
