<?php

namespace Amethyst\Observers;

use Amethyst\Models\Workflow;

class WorkflowObserver
{
    /**
     * Handle the Workflow "created" event.
     *
     * @param  \App\Workflow  $workflow
     * @return void
     */
    public function created(Workflow $workflow)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the Workflow "updated" event.
     *
     * @param  \App\Workflow  $workflow
     * @return void
     */
    public function updated(Workflow $workflow)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the Workflow "deleted" event.
     *
     * @param  \App\Workflow  $workflow
     * @return void
     */
    public function deleted(Workflow $workflow)
    {
        app('amethyst.action')->starter();
    }
}