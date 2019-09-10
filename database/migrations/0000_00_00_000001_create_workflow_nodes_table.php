<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowNodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.action.data.workflow-node.table'), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('workflow_id')->unsigned();
            $table->foreign('workflow_id')->references('id')->on(Config::get('amethyst.action.data.workflow.table'));

            $table->string('target_type');
            $table->integer('target_id')->unsigned();

            $table->text('data')->nullable();
            $table->text('output')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('amethyst.action.data.workflow-node.table'));
    }
}
