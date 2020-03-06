<?php

return [
    'table'      => 'action',
    'comment'    => 'Action',
    'model'      => Amethyst\Models\Action::class,
    'schema'     => Amethyst\Schemas\ActionSchema::class,
    'repository' => Amethyst\Repositories\ActionRepository::class,
    'serializer' => Amethyst\Serializers\ActionSerializer::class,
    'validator'  => Amethyst\Validators\ActionValidator::class,
    'authorizer' => Amethyst\Authorizers\ActionAuthorizer::class,
    'faker'      => Amethyst\Fakers\ActionFaker::class,
    'manager'    => Amethyst\Managers\ActionManager::class,
];
