<?php

namespace Amethyst\Http\Controllers\Admin;

use Amethyst\Api\Http\Controllers\RestManagerController;
use Amethyst\Api\Http\Controllers\Traits as RestTraits;
use Amethyst\Managers\WorkflowNodeManager;

class WorkflowNodesController extends RestManagerController
{
    use RestTraits\RestCommonTrait;

    /**
     * The class of the manager.
     *
     * @var string
     */
    public $class = WorkflowNodeManager::class;
}
