<?php

namespace Amethyst\Events;

use Amethyst\Models\WorkflowState;

class WorkflowDone
{
    /**
     * @var mixed
     */
    public $user;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var WorkflowState
     */
    public $workflowState;

    /**
     * @param WorkflowState $workflowState
     * @param mixed         $user
     */
    public function __construct(WorkflowState $workflowState, $data, $user)
    {
        $this->workflowState = $workflowState;
        $this->data = $data;
        $this->user = $user;
    }
}
