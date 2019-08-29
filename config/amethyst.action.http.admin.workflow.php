<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\WorkflowsController::class,
    'router'     => [
        'as'     => 'workflow.',
        'prefix' => '/workflows',
    ],
];
