<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Http configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the routes
    |
    */
    'app' => [
        'workflow' => [
            'enabled'    => true,
            'controller' => Amethyst\Http\Controllers\WorkflowController::class,
            'router'     => [
                'prefix' => '/data/workflow/execute',
                'as'     => 'workflow.execute.',
            ],
        ],
    ],
];
