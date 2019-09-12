<?php

namespace Amethyst\Actions;

use Closure;
use Illuminate\Support\Facades\Log as Logger;
use Railken\Bag;
use Railken\Template\Generators\TextGenerator;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;
use Amethyst\Models\WorkflowNode;
use Railken\LaraEye\Filter;

class Manager extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
		$generator = new TextGenerator;

    	$parameters = json_decode($generator->generateAndRender(json_encode($data->parameters), $data->toArray()));

    	$manager = app('amethyst')->findManagerByName($data->name);
    	$manager = new $manager;

    	$filter = new Filter($manager->newEntity()->getTable(), ['*']);

    	if ($data->action === 'create') {
    		$manager->createOrFail($parameters);
    	}

    	if ($data->action === 'update' || $data->action === 'remove') {
    		$filter = $generator->generateAndRender($data->query, $data->toArray());
    		$query = $manager->getRepository()->newQuery();

    		$query = $filter->build($query, $filter);

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
