<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class ActionAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'action.create',
        Tokens::PERMISSION_UPDATE => 'action.update',
        Tokens::PERMISSION_SHOW   => 'action.show',
        Tokens::PERMISSION_REMOVE => 'action.remove',
    ];
}
