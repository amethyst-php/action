<?php

namespace Amethyst\Schemas;

use Amethyst\Managers\WorkflowManager;
use Railken\Lem\Attributes;
use Railken\Lem\Schema;

class WorkflowNodeSchema extends Schema
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
            \Amethyst\Core\Attributes\DataNameAttribute::make('target_type')
                ->setRequired(true),
            Attributes\MorphToAttribute::make('target_id')
                ->setRelationKey('target_type')
                ->setRelationName('target')
                ->setRelations(app('amethyst')->getDataManagers())
                ->setRequired(true),
            Attributes\YamlAttribute::make('data'),
            Attributes\YamlAttribute::make('output'),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
            Attributes\DeletedAtAttribute::make(),
        ];
    }
}
