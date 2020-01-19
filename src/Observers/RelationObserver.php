<?php

namespace Amethyst\Observers;

use Amethyst\Models\Relation;

class RelationObserver
{
    /**
     * Handle the Relation "created" event.
     *
     * @param \App\Relation $relation
     *
     * @return void
     */
    public function created(Relation $relation)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the Relation "updated" event.
     *
     * @param \App\Relation $relation
     *
     * @return void
     */
    public function updated(Relation $relation)
    {
        app('amethyst.action')->starter();
    }

    /**
     * Handle the Relation "deleted" event.
     *
     * @param \App\Relation $relation
     *
     * @return void
     */
    public function deleted(Relation $relation)
    {
        app('amethyst.action')->starter();
    }
}
