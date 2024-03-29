<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowNodeStatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.action.data.workflow-node-state.table'), function (Blueprint $table) {
            $table->id();

            $table->integer('workflow_state_id')->unsigned();
            $table->foreign('workflow_state_id')->references('id')->on(Config::get('amethyst.action.data.workflow-state.table'));

            $table->integer('workflow_node_id')->unsigned();
            $table->foreign('workflow_node_id')->references('id')->on(Config::get('amethyst.action.data.workflow-node.table'));

            $table->string('state');
            $table->longText('data')->nullable();
            $table->text('input')->nullable();
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
        Schema::dropIfExists(Config::get('amethyst.action.data.workflow-node-state.table'));
    }
}
