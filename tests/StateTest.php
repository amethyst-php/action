<?php

namespace Amethyst\Tests;

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\RelationManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Tests\DummyEvent;
use Symfony\Component\Yaml\Yaml;

class StateTest extends BaseTest
{
    /**
     * Test basic workflow.
     */
    public function testLogEvent()
    {
        $actionManager = new ActionManager();
        $workflowManager = new WorkflowManager();
        $workflowNodeManager = new WorkflowNodeManager();
        $relationManager = new RelationManager();

        $dataAction = $actionManager->createOrFail([
            'name'    => 'Data Manipulation',
            'payload' => Yaml::dump([
                'class'     => 'data',
                'arguments' => [],
            ]),
        ])->getResource();

        $eventListenerAction = $actionManager->createOrFail([
            'name'    => 'Event Listener',
            'payload' => Yaml::dump([
                'class'     => 'listener',
                'arguments' => [],
            ]),
        ])->getResource();

        $workflow = $workflowManager->createOrFail([
            'name' => 'Log events',
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output'       => Yaml::dump(['event']),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name' => 'foo',
                'action'   => 'create',
                'parameters' => [
                    'name' => "{{ event.message }}",
                ]
            ])
        ])->getResource();

        $relationManager->createOrFail([
            'source_type'    => 'workflow',
            'source_id'      => $workflow->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node1->id,
        ]);

        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node1->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node2->id,
        ]);


        event(new DummyEvent('Yeah!'));
        event(new DummyEvent('Not Anymore!'));

        \Log::info("Creating second workflow");

        $workflow = $workflowManager->createOrFail([
            'name' => 'Should work with 2 workflow',
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output'       => Yaml::dump(['event']),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name' => 'foo',
                'action'   => 'create',
                'parameters' => [
                    'name' => "This is a random name {{ event.message }}",
                ]
            ])
        ])->getResource();

        $relationManager->createOrFail([
            'source_type'    => 'workflow',
            'source_id'      => $workflow->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node1->id,
        ]);

        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node1->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node2->id,
        ]);
        
        event(new DummyEvent('Double!'));
    }
}
