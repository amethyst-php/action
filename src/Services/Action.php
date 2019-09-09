<?php

namespace Amethyst\Services;

use Amethyst\Managers\AggregatorManager;
use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Managers\WorkflowNodeStateManager;
use Amethyst\Managers\WorkflowStateManager;
use Railken\Bag;
use Symfony\Component\Yaml\Yaml;

class Action
{
    protected $types = [];

    public function __construct()
    {
        $this->workflowNodeStateManager = new WorkflowNodeStateManager();
        $this->workflowStateManager = new WorkflowStateManager();
        $this->aggregatorManager = new AggregatorManager();
    }

    public function addType(string $name, string $class)
    {
        $this->types[$name] = $class;
    }

    public function getType(string $name)
    {
        return $this->types[$name] ?? null;
    }

    public function starter()
    {
        $workflowNodeManager = new WorkflowNodeManager();

        $aggregators = $this->aggregatorManager->getRepository()->newQuery()->where('source_type', 'workflow')->get();

        $aggregators->filter(function ($aggregator) {
            return $aggregator->source->enabled;
        });

        $this->dispatchByAggregators($aggregators, null);

        $this->dispatchByWorkflowNodeState();
    }

    public function dispatchByAggregators($aggregators, $workflowState)
    {
        $aggregators->map(function ($aggregator) use ($workflowState) {
            return $this->dispatch($aggregator->aggregate, $workflowState);
        });
    }

    public function dispatchByWorkflowNodeState()
    {
        $states = $this->workflowNodeStateManager->getRepository()
            ->newQuery()
            ->where('state', 'idle');
    }

    public function dispatch($workflowNode, $workflowState)
    {
        $data = $workflowNode->data;

        $action = $workflowNode->target;

        $payload = (object) Yaml::parse($action->payload);

        print_r($payload);

        $class = $this->getType($payload->class);

        $actioner = new $class(...$payload->arguments);

        $data = Yaml::parse($workflowNode->data);
        $actioner->setData($data);

        // If a workflowState is defined than this is not the first time
        // we hit this workflow instance, let's create
        if ($workflowState) {
            $this->workflowNodeStateManager->createOrFail([
                'workflow_node_id'  => $workflowNode->id,
                'workflow_state_id' => $workflowState->id,
                'state'             => 'idle',
            ]);
        }

        $actioner->dispatch(function ($data) use ($workflowNode) {
            $workflowState = $this->workflowStateManager->updateOrCreateOrFail([
                'workflow_id' => $workflow->id,
            ], [
                'state' => 'running',
            ])->getResource();

            $workflowNodeState = $this->workflowNodeStateManager->updateOrCreateOrFail([
                'workflow_node_id'  => $workflowNode->id,
                'workflow_state_id' => $workflowState->id,
            ], [
                'state' => 'executed',
            ])->getResource();

            return $this->dispatchByAggregators($this->aggregatorManager
                ->getRepository()
                ->newQuery()
                ->where('source_type', 'workflow-node')
                ->where('source_id', $workflowNode)
                ->where('aggregate_type', 'workflow-node')
                ->get(), $workflowState);
        }, new Bag());
    }
}
