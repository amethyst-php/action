<?php

namespace Amethyst\Actions;

use Closure;
use Illuminate\Support\Facades\Log as Logger;
use Amethyst\Services\Bag;
use Railken\Template\Generators\TextGenerator;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;
use Amethyst\Models\WorkflowNode;
use Railken\LaraEye\Filter;

class Manager extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $parameters = $data->parameters;

        $manager = app('amethyst')->findManagerByName($data->name);
        $manager = new $manager;

        $filter = new Filter($manager->newEntity()->getTable(), ['*']);

        \Log::info(sprintf("Workflow - Manager: %s, %s, %s", $data->action, $data->name, json_encode($parameters)));

        if ($data->action === 'create') {
            $manager->createOrFail($parameters);
        }

        if ($data->action === 'update' || $data->action === 'remove') {
            $string = $data->query;
            $query = $manager->getRepository()->newQuery();

            $filter->build($query, $string);

            foreach ($query->get() as $record) {
                if ($data->action === 'update') {
                    $manager->update($record, $parameters);
                }

                if ($data->action === 'remove') {
                    $manager->remove($record);
                }
            }
        }

        $this->done($data);
    }
}
