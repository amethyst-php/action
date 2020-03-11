<?php

namespace Amethyst\Actions;

use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;
use Illuminate\Support\Facades\Log;
use Railken\LaraEye\Filter;

class Manager extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $parameters = $data->parameters;

        $manager = app('amethyst')->findManagerByName($data->name);

        $filter = new Filter($manager->newEntity()->getTable(), ['*']);

        Log::debug(sprintf('Workflow - Manager: %s, %s, %s', $data->action, $data->name, json_encode($parameters)));

        if ($data->action === 'create') {
            $manager->createOrFail($parameters);
        }

        if (in_array($data->action, ['update', 'remove'], true)) {
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

        if (in_array($data->action, ['get', 'first'], true)) {
            $string = $data->query;
            $query = $manager->getRepository()->newQuery();

            $filter->build($query, $string);

            if ($data->action === 'get') {
                $data->set('resources', $query->get());
            }

            if ($data->action === 'first') {
                $data->set('resource', $query->first());
            }
        }

        $this->done($data);
    }
}
