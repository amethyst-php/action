<?php

namespace Amethyst\Jobs;

use Amethyst\Models\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Railken\Template\Generators;
use Symfony\Component\Yaml\Yaml;

class Action implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $action;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param Action  $action
     * @param array $data
     */
    public function __construct(Action $action, array $data = [])
    {
        $this->action = $action;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $action = $this->action;
        $data = $this->data;

        $generator = new Generators\TextGenerator();

        $payload = json_decode(strval(json_encode(Yaml::parse($generator->generateAndRender($action->payload, $data)))));

        $actioner = new $payload->class();
        $method = $payload->method;

        $actioner->$method(...$payload->arguments);
    }
}
