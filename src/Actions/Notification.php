<?php

namespace Amethyst\Actions;

use Amethyst\Models\Relation;
use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;
use nicoSWD\Rules\Rule;
use Illuminate\Support\Facades\Log;
use Amethyst\Notifications\BaseNotification;

class Notification extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        print_r($data);

        $agents = app('amethyst')->get($data->get('agent.data'))->newEntity()->filter($data->get('agent.filter'))->get();

        foreach ($agents as $agent) {
            $agent->notify(new BaseNotification($data->get('message'), $data->get('vars')));
        }

        $this->done($data);
    }
}
