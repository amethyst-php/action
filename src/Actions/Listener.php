<?php

namespace Amethyst\Actions;

use Closure;
use Railken\Bag;
use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;

class Listener extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
    	$this->id = $nodeState ? "S".$nodeState->id : "N".$workflowNode->id;
    	\Log::info(sprintf("Workflow - Handling event: %s", $this->id));

        $this->addEvent($this->id, $data->event, function ($event) use ($data) {
            $this->done($data->merge(new Bag(['event' => $event])));
            // $this->removeEvent($this->id);
        });

        $this->release($data);
    }
}