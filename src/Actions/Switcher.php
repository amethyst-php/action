<?php

namespace Amethyst\Actions;

use Closure;
use Illuminate\Support\Facades\Log as Logger;
use Railken\Bag;
use Railken\Template\Generators\TextGenerator;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;
use Amethyst\Models\Relation;
use Amethyst\Models\WorkflowNode;
use nicoSWD\Rules\Rule;

class Switcher extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
		$generator = new TextGenerator;

        $nextNodes = (new Relation)
            ->where('source_type', 'workflow-node')
            ->where('source_id', $workflowNode->id)
            ->where('target_type', 'workflow-node')
            ->get()
            ->map(function ($relation) {
                return $relation->target;
            });

        // For each sibling compare id and condition
        $nextNodes = $nextNodes->filter(function (WorkflowNode $sibling) use ($generator, $data) {
            $expression = $generator->generateAndRender($data->channels[$sibling->id], $data->toArray());

            print_r($expression);
            $rule = new Rule($expression, []);

            return $rule->isTrue();
        })->map(function (WorkflowNode $sibling) {
            return $sibling->id;
        });

        $this->done($data, $nextNodes);
    }
}
