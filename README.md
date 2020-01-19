# amethyst-action

[![Action Status](https://github.com/amethyst-php/action/workflows/test/badge.svg)](https://github.com/amethyst-php/action/actions)

[Amethyst](https://github.com/amethyst-php/amethyst) package.

# Requirements

PHP 7.1 and later.

## Installation

You can install it via [Composer](https://getcomposer.org/) by typing the following command:

```bash
composer require amethyst/action
```

The package will automatically register itself.

## Documentation

[Read](docs/index.md)

## Testing

Configure the .env file before launching `./vendor/bin/phpunit`


## Dummy

Let's create a simple workflow, the goal is to log the message of an event when is fired.

`app/Events/DummyEvent.php`
```php
<?php

namespace App\Events;


class DummyEvent
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getData()
    {
        return [
            'message' => $this->message
        ];
    }
}
```

`app/Actions/LogAction.php`
```php
<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Amethyst\Actions\Action;
use Railken\Bag;

class LogAction extends Action
{
    public function requires()
    {
        return [
            'message' => 'text'
        ];
    }

    public function dispatch(Closure $next, Bag $data) 
    {
        Log::info($data->message);

        $next($data);
    }
}
```

`app/Actions/EventListenerAction.php`
```php
<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Amethyst\Actions\Action;
use Illuminate\Support\Facades\Event;
use Railken\Bag;

class EventListenerAction extends Action
{
    protected $event;

    public function dispatch(Closure $next, Bag $data) 
    {
        Event::listen([$this->data->event], function ($event_name, $events) use ($next, $data) {
            $next($data->merge(new Bag($events[0]->getData())));
        });
    }
}
```

Now remains only data entry
```php

use Amethyst\Managers\ActionManager;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Managers\WorkflowNode;
use Amethyst\Managers\AggregatorManager;
use App\Actions\LogAction;
use App\Actions\EventListenerAction;
use App\Events\DummyEvent;
use Symfony\Component\Yaml\Yaml;

app('amethyst.workflow')->addType('log', LogAction::class);
app('amethyst.workflow')->addType('event-listener', EventListenerAction::class);

$actionManager = new ActionManager();

$logAction = $actionManager->createOrFail([
    'name' => 'Log',
    'payload' => Yaml::dump([
        'class' => 'log',
        'arguments' => []
    ])
])->getResource();

$eventListenerAction = $actionManager->createOrFail([
    'name' => 'Event Listener',
    'payload' => Yaml::dump([
        'class' => 'event-listener',
        'arguments' => []
    ])
])->getResource();


$workflowManager = new WorkflowManager();
$aggregatorManager = new AggregatorManager();

$workflow = $workflowManager->createOrFail([
    'name' => 'Log events'
])->getResource();


$node1 = $workflowNodeManager->createOrFail([
    'workflow_id' => $workflow->id,
    'target_type' => 'action',
    'target_id' => $eventListeneAction->id,
    'data' => Yaml::dump([
        'event' => DummyEvent::class
    ]),
])->getResource();

$node2 = $workflowNodeManager->createOrFail([
    'workflow_id' => $workflow->id,
    'target_type' => 'action',
    'target_id' => $logAction->id
])->getResource();

$aggregatorManager->createOrFail([
    'source_type' => 'workflow-node',
    'source_id' => $node1->id,
    'aggregate_type' => 'workflow-node',
    'aggregate_type' => $node2->id
]);

```