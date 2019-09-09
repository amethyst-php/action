<?php

namespace Amethyst\Schemas;

use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Managers\WorkflowStateManager;
use Railken\Lem\Attributes;
use Railken\Lem\Schema;

class WorkflowNodeStateSchema extends Schema
{
    /**
     * Get all the attributes.
     *
     * @var array
     */
    public function getAttributes()
    {
        return [
            Attributes\IdAttribute::make(),
            Attributes\BelongsToAttribute::make('workflow_state_id')
                ->setRelationName('workflow_state')
                ->setRelationManager(WorkflowStateManager::class)
                ->setRequired(true),
            Attributes\BelongsToAttribute::make('workflow_node_id')
                ->setRelationName('workflow_node')
                ->setRelationManager(WorkflowNodeManager::class)
                ->setRequired(true),
            Attributes\EnumAttribute::make('state', [
                'idle',
                'executed',
            ])->setRequired(true),
            Attributes\LongTextAttribute::make('data'),
            Attributes\LongTextAttribute::make('input'),
            Attributes\LongTextAttribute::make('output'),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
            Attributes\DeletedAtAttribute::make(),
        ];
    }
}
