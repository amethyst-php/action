<?php

namespace Amethyst\Jobs;

use Amethyst\Actions\Action as BaseAction;
use Amethyst\Services\Bag;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Action implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $action;
    protected $data;
    protected $next;

    /**
     * Create a new job instance.
     *
     * @param \Railken\Bag $data
     */
    public function __construct(BaseAction $action, Closure $next, Bag $data)
    {
        $this->action = $action;
        $this->next = $next;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->action->storeState('running');
        $this->action->handle($this->next, $this->data);
    }
}
