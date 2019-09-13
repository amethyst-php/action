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

        $switcherAction = $actionManager->createOrFail([
            'name'    => 'Switcher',
            'payload' => Yaml::dump([
                'class'     => 'switcher',
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
    }

    public function testSwitcher()
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

        $switcherAction = $actionManager->createOrFail([
            'name'    => 'Switcher',
            'payload' => Yaml::dump([
                'class'     => 'switcher',
                'arguments' => [],
            ]),
        ])->getResource();


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

        $node3 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name' => 'foo',
                'action'   => 'create',
                'parameters' => [
                    'name' => "Street 1: Event {{ event.message }}",
                ]
            ])
        ])->getResource();

        $node4 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name' => 'foo',
                'action'   => 'create',
                'parameters' => [
                    'name' => "Street 2: Event {{ event.message }}",
                ]
            ])
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $switcherAction->id,
            'data'        => Yaml::dump([
                'channels' => [
                    $node3->id => "'{{ event.message }}' === '1'",
                    $node4->id => "'{{ event.message }}' === '2'"
                ]
            ]),
            'output'       => Yaml::dump(['event']),
        ])->getResource();

        $node5 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name' => 'foo',
                'action'   => 'create',
                'parameters' => [
                    'name' => "The end",
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

        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node2->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node3->id,
        ]);

        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node2->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node4->id,
        ]);

        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node4->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node5->id,
        ]);
        
        $relationManager->createOrFail([
            'source_type'    => 'workflow-node',
            'source_id'      => $node3->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node5->id,
        ]);

        event(new DummyEvent('1')); 
    }


    /**
     * Test basic workflow.
     */
    public function testHttp()
    {
        $actionManager = new ActionManager();
        $workflowManager = new WorkflowManager();
        $workflowNodeManager = new WorkflowNodeManager();
        $relationManager = new RelationManager();

        $httpAction = $actionManager->createOrFail([
            'name'    => 'Http',
            'payload' => Yaml::dump([
                'class'     => 'http',
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
            'name' => 'Send http call',
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
            'target_id'   => $httpAction->id,
            'output'       => Yaml::dump(['response']),
            'data'        => Yaml::dump([
                'url' => 'https://api.github.com/orgs/octokit/repos',
                'method' => 'GET',
                'headers' => [
                    'test' => 'Hello'
                ],
                'query' => [
                    'param' => "I'm a simple param"
                ],
                'json' => true
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

        event(new DummyEvent("It's time for a new request!"));
    }
}
