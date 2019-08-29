<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class WorkflowStateAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'workflow-state.create',
        Tokens::PERMISSION_UPDATE => 'workflow-state.update',
        Tokens::PERMISSION_SHOW   => 'workflow-state.show',
        Tokens::PERMISSION_REMOVE => 'workflow-state.remove',
    ];
}
