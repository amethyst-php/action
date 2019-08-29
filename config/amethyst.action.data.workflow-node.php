<?php

return [
    'table'      => 'amethyst_workflow_nodes',
    'comment'    => 'WorkflowNode',
    'model'      => Amethyst\Models\WorkflowNode::class,
    'schema'     => Amethyst\Schemas\WorkflowNodeSchema::class,
    'repository' => Amethyst\Repositories\WorkflowNodeRepository::class,
    'serializer' => Amethyst\Serializers\WorkflowNodeSerializer::class,
    'validator'  => Amethyst\Validators\WorkflowNodeValidator::class,
    'authorizer' => Amethyst\Authorizers\WorkflowNodeAuthorizer::class,
    'faker'      => Amethyst\Fakers\WorkflowNodeFaker::class,
    'manager'    => Amethyst\Managers\WorkflowNodeManager::class,
];
