<?php

namespace Amethyst\Actions;

use Closure;
use Amethyst\Services\Bag;
use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;

class Listener extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
    	$this->id = $nodeState ? "S".$nodeState->id : "N".$workflowNode->id;
    	\Log::info(sprintf("Workflow - Handling event: %s", $data->event));

        $this->addEvent($this->id, $data->event, function ($event) use ($data) {
            \Log::info(sprintf("Workflow - Reading event: %s", $data->event));

            $this->done($data->merge(new Bag(['event' => $event])));
            // $this->removeEvent($this->id);
        });

        $this->release($data);
    }
}