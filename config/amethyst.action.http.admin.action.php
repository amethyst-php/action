<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\ActionsController::class,
    'router'     => [
        'as'     => 'action.',
        'prefix' => '/actions',
    ],
];
