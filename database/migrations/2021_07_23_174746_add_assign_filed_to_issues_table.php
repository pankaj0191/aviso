<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignFiledToIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->integer('assign_id')->nullable()->index()->after('owner_type');
            $table->string('start_date')->nullable()->after('assign_id');
            $table->string('end_date')->nullable()->after('start_date');
            $table->string('tag')->nullable()->after('end_date');
            $table->text('likes')->nullable()->after('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('assign_id', 'start_date', 'end_date', 'tag', 'like');
        });
    }
}
