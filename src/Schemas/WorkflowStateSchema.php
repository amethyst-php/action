<?php

namespace Amethyst\Schemas;

use Railken\Lem\Attributes;
use Railken\Lem\Schema;
use Amethyst\Managers\WorkflowManager;

class WorkflowStateSchema extends Schema
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
            Attributes\BelongsToAttribute::make('workflow_id')
                ->setRelationName('workflow')
                ->setRelationManager(WorkflowManager::class)
                ->setRequired(true),
            Attributes\EnumAttribute::make('state', [
                'running',
                'executed',
            ])->setRequired(true),
            Attributes\LongTextAttribute::make('data'),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
            Attributes\DeletedAtAttribute::make(),
        ];
    }
}
