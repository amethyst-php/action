<?php

namespace Amethyst\Services;

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\RelationManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNodeManager;
use Symfony\Component\Yaml\Yaml;

class Node
{
    public $workflow;
    public $lastNode;

    public function __construct()
    {
        $this->actionManager = new ActionManager();
        $this->workflowManager = new WorkflowManager();
        $this->workflowNodeManager = new WorkflowNodeManager();
        $this->relationManager = new RelationManager();
    }

    public static function new(string $name)
    {
        $this->workflow = $this->workflowManager->createOrFail([
            'name' => $this->name,
        ])->getResource();
        $this->lastNode = $this->workflow;
    }

    public function next()
    {
        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output' => Yaml::dump(['event']),
        ])->getResource();
    }
}
