<?php

return [
    'table'      => 'workflow',
    'comment'    => 'Workflow',
    'model'      => Amethyst\Models\Workflow::class,
    'schema'     => Amethyst\Schemas\WorkflowSchema::class,
    'repository' => Amethyst\Repositories\WorkflowRepository::class,
    'serializer' => Amethyst\Serializers\WorkflowSerializer::class,
    'validator'  => Amethyst\Validators\WorkflowValidator::class,
    'authorizer' => Amethyst\Authorizers\WorkflowAuthorizer::class,
    'faker'      => Amethyst\Fakers\WorkflowFaker::class,
    'manager'    => Amethyst\Managers\WorkflowManager::class,
];
