<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Project extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('project_statuses', function (Blueprint $table) {
          $table->increments('id')->unsigned();
          $table->integer('project_id');
          $table->string('status');
          $table->text('description');
          $table->timestamps();
      });
      Schema::create('projects', function (Blueprint $table) {
          $table->increments('id')->unsigned();
          $table->string('title');
          $table->string('avatar');
          $table->string('background');
          $table->text('description');
          $table->string('version');
          // Client = OAuthClient
          $table->integer('client_id')->default(0);
          $table->string('url')->default('');
          $table->string('direct_login_url')->default('');
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
      Schema::dropIfExists('project_status');
      Schema::dropIfExists('projects');
    }
}
