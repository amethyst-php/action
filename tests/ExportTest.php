<?php

namespace Amethyst\Tests;

class ExportTest extends BaseTest
{
    public function testExport()
    {
        $workflow = app('amethyst')->get('workflow')->createOrFail([
            'name' => 'test-1',
        ])->getResource();

        $node = $workflow->next('exporter', [
            'type'     => 'csv',
            'data'     => 'foo',
            'filter'   => '',
            'filename' => 'myFile.csv',
        ], [], [
            'body' => [
                'id'     => '{{ resource.id }}',
                'myName' => '{{ resource.name }}',
            ],
        ]);

        app('amethyst')->get('foo')->createOrFail([
            'name' => 'Hello',
        ]);

        app('amethyst.action')->dispatchByWorkflow($workflow);

        $result = app('amethyst')->get('file')->getRepository()->findAll();

        $this->assertEquals(1, $result->count());
    }
}
