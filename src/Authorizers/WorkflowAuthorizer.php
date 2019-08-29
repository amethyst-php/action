<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class WorkflowAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'workflow.create',
        Tokens::PERMISSION_UPDATE => 'workflow.update',
        Tokens::PERMISSION_SHOW   => 'workflow.show',
        Tokens::PERMISSION_REMOVE => 'workflow.remove',
    ];
}
