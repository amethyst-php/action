<?php

namespace Amethyst\Services;

use Amethyst\Managers\RelationManager;
use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Managers\WorkflowNodeStateManager;
use Amethyst\Managers\WorkflowStateManager;
use Amethyst\Models\Relation;
use Amethyst\Services\Bag;
use Symfony\Component\Yaml\Yaml;

class Action
{
    protected $types = [];
    protected $enabled = false;

    public function __construct()
    {
        $this->workflowNodeStateManager = new WorkflowNodeStateManager();
        $this->workflowStateManager = new WorkflowStateManager();
        $this->relationManager = new RelationManager();
        $this->workflowNodeManager = new WorkflowNodeManager();
    }

    public function enable()
    {
        $this->enabled = true;
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
        \Log::info("Workflow - Checking");
        $this->dispatchByRelations();
        $this->dispatchByWorkflowNodeState();
    }

    public function dispatchByRelations()
    {
        $results = $this->relationManager
            ->getRepository()
            ->newQuery()
            ->where('source_type', 'workflow')
            ->where('target_type', 'workflow-node')
            ->get();

        \Log::info(sprintf("Workflow - Checking relations to start: %s", $results->count()));

        $results->filter(function ($relation) {
            return $relation->source->enabled;
        })->map(function ($relation) {
            return $this->dispatch($relation->target);
        });
    }

    public function dispatchByWorkflowNodeState()
    {
        $this->workflowNodeStateManager
            ->getRepository()
            ->newQuery()
            ->where('state', 'wait')
            ->get()
            ->map(function ($workflowNodeState) {
                return $this->dispatch($workflowNodeState->workflow_node, $workflowNodeState);
            });
    }

    public function dispatchBySameWorkflowNodeState($workflowState)
    {
        $this->workflowNodeStateManager
            ->getRepository()
            ->newQuery()
            ->where('workflow_state_id', $workflowState->id)
            ->where('state', 'wait')
            ->get()
            ->map(function ($workflowNodeState) {
                return $this->dispatch($workflowNodeState->workflow_node, $workflowNodeState);
            });
    }

    public function dispatch($workflowNode, $workflowNodeState = null)
    {
        \Log::info(sprintf(
            "Workflow - Dispatching Workflow %s, WorkflowNode: %s with state %s", 
            $workflowNode->workflow->id,
            $workflowNode->id, 
            $workflowNodeState->id ?? null
        ));

        // This is currently running
        // Doing this way well'avoid to re-execute twice the same worfklow
        if ($workflowNodeState) {
            $workflowNodeState->state = 'run';
            $workflowNodeState->save();
        }

        $data = $workflowNode->data;

        $action = $workflowNode->target;

        $payload = (object) Yaml::parse($action->payload);

        if (!isset($payload->class)) {
            \Log::warning(sprintf("Error with workflow, missing class"));
            return;
        }

        $class = $this->getType($payload->class);

        $executed = function ($data, $allowedNextNodes = null) use ($workflowNode, $workflowNodeState) {

            \Log::info(sprintf(
                "Workflow - Executing Workflow %s, WorkflowNode: %s with state %s", 
                $workflowNode->workflow->id,
                $workflowNode->id, 
                $workflowNodeState->id ?? null
            ));

            // Define a new state for the workflow
            if (!$workflowNodeState) {
                $workflowState = $this->workflowStateManager->createOrFail([
                    'workflow_id'       => $workflowNode->workflow->id,
                    'state'             => 'run',
                ])->getResource();
            } else {
                $workflowState = $workflowNodeState->workflow_state;
            }

            // Data is filtered based on output workflowNode
            $output = new Bag((array) Yaml::parse((string) $workflowNode->output));

            // Dot notation is applied
            // So key => target.value filter can be used
            foreach ($output as $key => $value) {
                $output->set($key, \Illuminate\Support\Arr::get($data->toArray(), $value)); 
            }

            $data = new Bag($output);

            // Define a new state for the node as done
            // First time executed, already done
            if (!$workflowNodeState) {
                $workflowNodeState = $this->workflowNodeStateManager->createOrFail([
                    'workflow_node_id'  => $workflowNode->id,
                    'workflow_state_id' => $workflowState->id,
                    'state'             => 'done',
                    'data'              => serialize($data)
                ])->getResource();
            } else {
                $workflowNodeState->data = serialize($data);
                $workflowNodeState->state = 'done';
                $workflowNodeState->save();
            }

            // Set all next nodes as idles
            $nextNodes = (new Relation)
                ->where('source_type', 'workflow-node')
                ->where('source_id', $workflowNode->id)
                ->where('target_type', 'workflow-node')
                ->get();
                
            // If there is no next nodes, than the workflow instance should be terminated
            if ($nextNodes->count() === 0) {

                \Log::info(sprintf("Workflow - Terminating %s", $workflowNode->workflow->id));

                $workflowState->state = 'done';
                $workflowState->save();
            }

            if ($allowedNextNodes !== null) {
                $nextNodes = $nextNodes->filter(function ($relation) use ($allowedNextNodes) {
                    return in_array($relation->target->id, $allowedNextNodes->toArray());
                });
            }

            $nextNodes->map(function ($relation) use ($workflowState, $data) {

                $workflowNode = $relation->target;

                \Log::info(sprintf(
                    "Workflow - Activating siblings Workflow %s, WorkflowNode: %s", 
                    $workflowNode->workflow->id,
                    $workflowNode->id
                ));

                $this->workflowNodeStateManager->createOrFail([
                    'workflow_node_id'  => $workflowNode->id,
                    'workflow_state_id' => $workflowState->id,
                    'state'             => 'wait',
                    'data'              => serialize($data)
                ]);
            });

            // We now have a situation where the actioner is handled, with a node state already executed
            // and the overall workflow state in running

            // We know know that this workflow instance has moved, we can check immediately if the next
            // Node is available for dispatching instead of waiting the call of `starter`
            $this->dispatchBySameWorkflowNodeState($workflowState);
        };

        $released = function ($data) use ($workflowNodeState) {

            if ($workflowNodeState) {
                \Log::info(sprintf("Terminating - Executing WorkflowNode: %s with state %s", $workflowNodeState->workflow_node->id, $workflowNodeState->id ?? null));

                if ($workflowNodeState) {
                    $workflowNodeState->state = 'wait';
                    $workflowNodeState->data = serialize($data);
                    $workflowNodeState->save();
                }
            }

        };

        $actioner = new $class($executed, $released);

        $parsed = Yaml::parse((string) $workflowNode->data);
        $data = new Bag($parsed ?? []);
        $data = $data->merge($workflowNodeState ? unserialize($workflowNodeState->data) : []);

        $actioner->setData($data);

        $actioner->handle($data, $workflowNode, $workflowNodeState ? $workflowNodeState : null);
    }
}
