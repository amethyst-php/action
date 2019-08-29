<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\WorkflowStatesController::class,
    'router'     => [
        'as'     => 'workflow-state.',
        'prefix' => '/workflow-states',
    ],
];
