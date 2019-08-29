<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\WorkflowNodesController::class,
    'router'     => [
        'as'     => 'workflow-node.',
        'prefix' => '/workflow-nodes',
    ],
];
