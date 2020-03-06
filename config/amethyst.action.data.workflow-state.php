<?php

return [
    'table'      => 'workflow_state',
    'comment'    => 'WorkflowState',
    'model'      => Amethyst\Models\WorkflowState::class,
    'schema'     => Amethyst\Schemas\WorkflowStateSchema::class,
    'repository' => Amethyst\Repositories\WorkflowStateRepository::class,
    'serializer' => Amethyst\Serializers\WorkflowStateSerializer::class,
    'validator'  => Amethyst\Validators\WorkflowStateValidator::class,
    'authorizer' => Amethyst\Authorizers\WorkflowStateAuthorizer::class,
    'faker'      => Amethyst\Fakers\WorkflowStateFaker::class,
    'manager'    => Amethyst\Managers\WorkflowStateManager::class,
];
