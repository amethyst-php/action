<?php

namespace Amethyst\Actions;

use Amethyst\Events\NotificationEvent;
use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Notifications\BaseNotification;
use Amethyst\Services\Bag;

class Notification extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $agents = app('amethyst')->get($data->get('agent.data'))->newEntity()->filter($data->get('agent.filter'))->get();

        foreach ($agents as $agent) {
            $agent->notify(new BaseNotification($data->get('message'), $data->get('vars')));
            event(new NotificationEvent($agent, config('app.name'), $data->get('message')));
        }

        $this->done($data);
    }
}
