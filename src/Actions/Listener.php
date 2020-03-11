<?php

namespace Amethyst\Actions;

use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;
use Illuminate\Support\Facades\Log;

class Listener extends Action
{
    protected $id;

    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $this->id = $nodeState ? 'S'.$nodeState->id : 'N'.$workflowNode->id;

        Log::debug(sprintf('Workflow - Handling event: %s, internal id: %s', $data->event, $this->id));

        app('amethyst.action')->addEvent($this->id, $data->event, function ($event) use ($data) {
            Log::debug(sprintf('Workflow - Reading event: %s', $data->event));

            $this->done($data->merge(new Bag(['event' => $event])));
            // $this->removeEvent($this->id);
        });

        $this->release($data);
    }
}
