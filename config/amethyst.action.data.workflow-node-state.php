<?php

return [
    'table'      => 'amethyst_workflow_node_states',
    'comment'    => 'WorkflowNodeState',
    'model'      => Amethyst\Models\WorkflowNodeState::class,
    'schema'     => Amethyst\Schemas\WorkflowNodeStateSchema::class,
    'repository' => Amethyst\Repositories\WorkflowNodeStateRepository::class,
    'serializer' => Amethyst\Serializers\WorkflowNodeStateSerializer::class,
    'validator'  => Amethyst\Validators\WorkflowNodeStateValidator::class,
    'authorizer' => Amethyst\Authorizers\WorkflowNodeStateAuthorizer::class,
    'faker'      => Amethyst\Fakers\WorkflowNodeStateFaker::class,
    'manager'    => Amethyst\Managers\WorkflowNodeStateManager::class,
];
