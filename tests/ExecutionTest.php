<?php

namespace Amethyst\Tests;

class ExecutionTest extends BaseTest
{
    public function testExecution()
    {
        $workflow = app('amethyst')->get('workflow')->createOrFail([
            'name' => 'test-1',
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
            'name' => 'test-1',
        ])->getResource();

        $node = $workflow->next('data', [
            'action'     => 'create',
            'name'       => 'foo',
            'parameters' => [
                'name' => 'Hello my api friend',
            ],
        ]);

        $response = $this->json('POST', 'api/workflow/execute', [
            'query' => 'id eq 1',
        ]);

        $response->assertStatus(200);

        $result = app('amethyst')->get('foo')->getRepository()->findAll();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Hello my api friend', $result[0]->name);
    }
}
