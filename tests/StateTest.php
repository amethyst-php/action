<?php

namespace Amethyst\Tests;

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\RelationManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNodeManager;
use Symfony\Component\Yaml\Yaml;

class StateTest extends Base
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
            'autostart' => 1
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output' => Yaml::dump(['event']),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name'       => 'foo',
                'action'     => 'create',
                'parameters' => [
                    'name' => 'Nome: {{ event }}',
                ],
            ]),
        ])->getResource();

        $relationManager->createOrFail([
            'source_type' => 'workflow',
            'source_id'   => $workflow->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node1->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node1->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node2->id,
        ]);

        event(new DummyEvent('Yeah!'));
        event(new DummyEvent('Not Anymore!'));

        $this->assertEquals(
            "Nome: DummyEvent: Yeah!",
            app('amethyst')->get('foo')->getRepository()->newQuery()->where('id', 1)->first()->name
        );
        $this->assertEquals(
            "Nome: DummyEvent: Not Anymore!",
            app('amethyst')->get('foo')->getRepository()->newQuery()->where('id', 2)->first()->name
        );
        $this->assertEquals(
            2,
            app('amethyst')->get('foo')->getRepository()->newQuery()->count()
        );
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
            'autostart' => 1
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output' => Yaml::dump(['event']),
        ])->getResource();

        $node3 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name'       => 'foo',
                'action'     => 'create',
                'parameters' => [
                    'name' => 'Street 1: Event {{ event.message }}',
                ],
            ]),
        ])->getResource();

        $node4 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name'       => 'foo',
                'action'     => 'create',
                'parameters' => [
                    'name' => 'Street 2: Event {{ event.message }}',
                ],
            ]),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $switcherAction->id,
            'data'        => Yaml::dump([
                'channels' => [
                    $node3->id => "'{{ event.message }}' === '1'",
                    $node4->id => "'{{ event.message }}' === '2'",
                ],
            ]),
            'output' => Yaml::dump(['event']),
        ])->getResource();

        $node5 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name'       => 'foo',
                'action'     => 'create',
                'parameters' => [
                    'name' => 'The end',
                ],
            ]),
        ])->getResource();

        $relationManager->createOrFail([
            'source_type' => 'workflow',
            'source_id'   => $workflow->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node1->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node1->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node2->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node2->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node3->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node2->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node4->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node4->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node5->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node3->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node5->id,
        ]);

        event(new DummyEvent('1'));

        // Only Street 1 should be called since of the switcher
        $this->assertEquals(
            "Street 1: Event 1",
            app('amethyst')->get('foo')->getRepository()->newQuery()->where('id', 1)->first()->name
        );
        $this->assertEquals(
            "The end",
            app('amethyst')->get('foo')->getRepository()->newQuery()->where('id', 2)->first()->name
        );
        $this->assertEquals(
            2,
            app('amethyst')->get('foo')->getRepository()->newQuery()->count()
        );
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
            'name' => 'Send http call',
            'autostart' => 1
        ])->getResource();

        $node1 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $eventListenerAction->id,
            'data'        => Yaml::dump([
                'event' => DummyEvent::class,
            ]),
            'output' => Yaml::dump(['event']),
        ])->getResource();

        $node2 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $httpAction->id,
            'output'      => Yaml::dump(['response']),
            'data'        => Yaml::dump([
                'url'     => 'https://api.github.com/random',
                'method'  => 'GET',
                'headers' => [
                    'test' => 'Hello',
                ],
                'query' => [
                    'param' => "I'm a simple param",
                ],
                'json' => true,
            ]),
        ])->getResource();

        $node3 = $workflowNodeManager->createOrFail([
            'workflow_id' => $workflow->id,
            'target_type' => 'action',
            'target_id'   => $dataAction->id,
            'data'        => Yaml::dump([
                'name'       => 'foo',
                'action'     => 'create',
                'parameters' => [
                    'name' => 'Request Log',
                    'description' => 'Status: {{ response.status }}',
                ],
            ]),
        ])->getResource();

        $relationManager->createOrFail([
            'source_type' => 'workflow',
            'source_id'   => $workflow->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node1->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node1->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node2->id,
        ]);

        $relationManager->createOrFail([
            'source_type' => 'workflow-node',
            'source_id'   => $node2->id,
            'target_type' => 'workflow-node',
            'target_id'   => $node3->id,
        ]);


        event(new DummyEvent("It's time for a new request!"));

        $this->assertEquals(
            "Status: 404",
            app('amethyst')->get('foo')->getRepository()->newQuery()->where('id', 1)->first()->description
        );
        $this->assertEquals(
            1,
            app('amethyst')->get('foo')->getRepository()->newQuery()->count()
        );
    }
    /**
    public function testNodes()
    {
        $node = \Amethyst\Services\Node::workflow('Hello darkness my old friend');

        $node = $node->next('listener', [
            'event' => DummyEvent::class,
        ], ['event']);

        $node1 = $node->new('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'Oh, Hi!',
            ],
        ]);

        $node2 = $node->new('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'Oh, Hi!',
            ],
        ]);

        $node = $node->switch([
            [
                'node'      => $node1,
                'condition' => '{{ event.message }} === Hello',
            ],
            [
                'node'      => $node2,
                'condition' => '{{ event.message }} === Goodbye',
            ],
        ]);

        $node2 = $node->new('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'The end',
            ],
        ]);
    }
    */
}
