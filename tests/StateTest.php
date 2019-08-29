<?php

namespace Amethyst\Tests;

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNode;
use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Managers\AggregatorManager;
use App\Actions\LogAction;
use App\Actions\EventListenerAction;
use App\Events\DummyEvent;
use Symfony\Component\Yaml\Yaml;

class StateTest extends BaseTest
{
    /**
     * Test basic workflow
     */
    public function testLogEvent()
    {
        $actionManager = new ActionManager();
        $workflowManager = new WorkflowManager();
        $workflowNodeManager = new WorkflowNodeManager();
        $aggregatorManager = new AggregatorManager();

        $logAction = $actionManager->createOrFail([
            'name' => 'Log',
            'payload' => Yaml::dump([
                'class' => 'log',
                'arguments' => []
            ])
        ])->getResource();

        $eventListenerAction = $actionManager->createOrFail([
            'name' => 'Event Listener',
            'payload' => Yaml::dump([
                'class' => 'listener',
                'arguments' => []
            ])
        ])->getResource();

        $workflow = $workflowManager->createOrFail([
            'name' => 'Log events'
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id' => $eventListenerAction->id,
            'data' => Yaml::dump([
                'event' => DummyEvent::class
            ]),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id' => $logAction->id
        ])->getResource();

        $aggregatorManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id' => $node1->id,
            'aggregate_type' => 'workflow-node',
            'aggregate_id' => $node2->id
        ]);

        $aggregatorManager->createOrFail([
            'source_type' => 'workflow',
            'source_id' => $workflow->id,
            'aggregate_type' => 'workflow-node',
            'aggregate_id' => $node1->id
        ]);

        app('amethyst.action')->starter();
    }
}
