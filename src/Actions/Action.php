<?php

namespace Amethyst\Actions;

use Closure;
use Amethyst\Services\Bag;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;
use Amethyst\Models\WorkflowNode;

class Action
{
    protected $data;
    protected static $events;
    protected static $booted = false;

    public function __construct(Closure $executed, Closure $released)
    {
        $this->executed = $executed;
        $this->released = $released;
    }

    public function done(Bag $data, $nodes = null)
    {
        $closure = $this->executed;
        $closure($data, $nodes);
    }

    public function release(Bag $data)
    {
        $closure = $this->released;
        $closure($data);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Destroy the current action
     */
    public function destroy()
    {

    }
}
