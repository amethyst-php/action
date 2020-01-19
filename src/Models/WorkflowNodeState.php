<?php

namespace Amethyst\Models;

use Amethyst\Core\ConfigurableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Railken\Lem\Contracts\EntityContract;

class WorkflowNodeState extends Model implements EntityContract
{
    use SoftDeletes;
    use ConfigurableModel;

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $this->ini('amethyst.action.data.workflow-node-state');
        parent::__construct($attributes);
    }

    public function workflow_node(): BelongsTo
    {
        return $this->belongsTo(config('amethyst.action.data.workflow-node.model'));
    }

    public function workflow_state(): BelongsTo
    {
        return $this->belongsTo(config('amethyst.action.data.workflow-state.model'));
    }
}
