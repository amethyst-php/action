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
use Amethyst\Models\Workflow;

class DispatchWorkflow implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $workflow;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(Workflow $workflow, $data)
    {
        $this->workflow = $workflow;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        app('amethyst.action')->dispatchByWorkflow($this->workflow, $this->data);
    }
}
