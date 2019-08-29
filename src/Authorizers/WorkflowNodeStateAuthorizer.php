<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class WorkflowNodeStateAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'workflow-node-state.create',
        Tokens::PERMISSION_UPDATE => 'workflow-node-state.update',
        Tokens::PERMISSION_SHOW   => 'workflow-node-state.show',
        Tokens::PERMISSION_REMOVE => 'workflow-node-state.remove',
    ];
}
