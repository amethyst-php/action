<?php

namespace Amethyst\Tests;

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\RelationManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNodeManager;
use Symfony\Component\Yaml\Yaml;

class ExecutionTest extends BaseTest
{
    public function testExecution()
    {
        $workflow = app('amethyst')->get('workflow')->createOrFail([
            'name' => 'test-1'
        ])->getResource();

        $node = $workflow->next('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'Hello my friend',
            ],
        ]);

        app('amethyst.action')->dispatchByWorkflow($workflow);

        $result = app('amethyst')->get('foo')->getRepository()->findAll();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Hello my friend', $result[0]->name);
    }

    public function testExecutionApi()
    {
        $workflow = app('amethyst')->get('workflow')->createOrFail([
            'name' => 'test-1'
        ])->getResource();

        $node = $workflow->next('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'Hello my friend',
            ],
        ]);

        $response = $this->json("POST", "app/workflow/execute", [
            'query' => 'id eq 1'
        ]);

        $response->assertStatus(200);

        //app('amethyst.action')->dispatchByWorkflow($workflow);

        $result = app('amethyst')->get('foo')->getRepository()->findAll();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Hello my friend', $result[0]->name);
    }
}