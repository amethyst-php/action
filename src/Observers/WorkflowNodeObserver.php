<?php

namespace Amethyst\Observers;

use Amethyst\Models\WorkflowNode;

class WorkflowNodeObserver
{
    /**
     * Handle the WorkflowNode "created" event.
     *
     * @param  \App\WorkflowNode  $workflowNode
     * @return void
     */
    public function created(WorkflowNode $workflowNode)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the WorkflowNode "updated" event.
     *
     * @param  \App\WorkflowNode  $workflowNode
     * @return void
     */
    public function updated(WorkflowNode $workflowNode)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the WorkflowNode "deleted" event.
     *
     * @param  \App\WorkflowNode  $workflowNode
     * @return void
     */
    public function deleted(WorkflowNode $workflowNode)
    {
        app('amethyst.action')->starter();
    }
}