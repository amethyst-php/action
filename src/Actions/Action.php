<?php

namespace Amethyst\Actions;

use Closure;
use Railken\Bag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
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
        self::boot();
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

    public static function boot()
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;


        self::$events = Collection::make();

        Event::listen(['*'], function ($event, $events) {

            $event = $events[0];
    
            self::$events->filter(function ($evt) use ($event) {
                return $evt->class === get_class($event);
            })->map(function ($action) use ($event) {
                $closure = $action->execute;
                $closure($event);
            });
        });
    }

    public function addEvent(string $uid, string $event, Closure $closure)
    {
        self::$events[$uid] = (object) [
            'class'   => $event,
            'execute' => $closure
        ];
    }

    public function removeEvent(string $id)
    {
        unset(self::$events[$id]);
    }

    public function getEvents()
    {
        return self::$events;
    }

    /**
     * Destroy the current action
     */
    public function destroy()
    {

    }
}
