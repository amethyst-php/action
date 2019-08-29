<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\WorkflowNodeStatesController::class,
    'router'     => [
        'as'     => 'workflow-node-state.',
        'prefix' => '/workflow-node-states',
    ],
];
