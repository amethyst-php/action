<?php

namespace Amethyst\Models;

use Amethyst\Concerns\ActionNodeHelper;
use Amethyst\Core\ConfigurableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Railken\Lem\Contracts\EntityContract;

class WorkflowNode extends Model implements EntityContract
{
    use SoftDeletes;
    use ConfigurableModel;
    use ActionNodeHelper;

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $this->ini('amethyst.action.data.workflow-node');
        parent::__construct($attributes);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(config('amethyst.action.data.workflow.model'));
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function relations(): MorphToMany
    {
        return $this->morphToMany(
            WorkflowNode::class,
            'source',
            config('amethyst.relation.data.relation.table'),
            'source_id',
            'target_id'
        )
        ->withPivotValue('target_type', 'workflow-node')
        ->using(config('amethyst.relation.data.relation.model'))
        ->withPivotValue('key', 'relations');
    }
}
