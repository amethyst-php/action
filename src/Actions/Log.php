<?php

namespace Amethyst\Actions;

use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;
use Illuminate\Support\Facades\Log as Logger;
use Railken\Template\Generators\TextGenerator;

class Log extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        Logger::info((new TextGenerator())->generateAndRender($data->template, $data->toArray()));

        $this->done($data);
    }
}
