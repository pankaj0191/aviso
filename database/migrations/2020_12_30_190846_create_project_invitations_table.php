<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index();
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->string('email')->index()->nullable();
            $table->string('role')->nullable();
            $table->string('permission')->nullable();
            $table->string('token', 40)->unique();
            $table->timestamp('expire_at')->nullable();
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
        Schema::dropIfExists('project_invitations');
    }
}
