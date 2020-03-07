<?php

namespace Amethyst\Tests;

class NotificationTest extends BaseTest
{
    public function testNotification()
    {
        $workflow = app('amethyst')->get('workflow')->createOrFail([
            'name' => 'test-1',
        ])->getResource();

        $node = $workflow->next('notification', [
            'agent' => [
                'data' => 'foo',
                'filter' => 'id != 0'
            ],
            'message'    => 'foo',
            'vars'      => ['uhm']
        ]);
        
        app('amethyst')->get('foo')->createOrFail([
            'name' => "Hello"
        ]);


        app('amethyst.action')->dispatchByWorkflow($workflow);
    }

}
