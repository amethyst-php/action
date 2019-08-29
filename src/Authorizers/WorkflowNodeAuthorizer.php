<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class WorkflowNodeAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'workflow-node.create',
        Tokens::PERMISSION_UPDATE => 'workflow-node.update',
        Tokens::PERMISSION_SHOW   => 'workflow-node.show',
        Tokens::PERMISSION_REMOVE => 'workflow-node.remove',
    ];
}
