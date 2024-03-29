<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowStatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Config::get('amethyst.action.data.workflow-state.table'), function (Blueprint $table) {
            $table->id();

            $table->integer('workflow_id')->unsigned();
            $table->foreign('workflow_id')->references('id')->on(Config::get('amethyst.action.data.workflow.table'));

            $table->string('state');
            $table->binary('data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('amethyst.action.data.workflow-state.table'));
    }
}
