<?php

namespace Amethyst\Actions;

use Closure;
use Amethyst\Services\Bag;
use Railken\Template\Generators\TextGenerator;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Models\WorkflowState;
use Amethyst\Models\Relation;
use Amethyst\Models\WorkflowNode;
use nicoSWD\Rules\Rule;
use GuzzleHttp\HandlerStack;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use Symfony\Component\Yaml\Yaml;

class Http extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $time = microtime(true);

        $testHandler = new TestHandler();

        $logger = new Logger('guzzle.to.curl');
        $logger->pushHandler($testHandler);

        $handler = HandlerStack::create();
        $handler->after('cookies', new CurlFormatterMiddleware($logger));

        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
            'handler'     => $handler,
        ]);


        $parameters = [
            'headers' => $data->get('headers'),
            'form_params'    => $data->get('body'),
            'query'    => $data->get('query'),
        ];

        if ($data->get('json')) {
            $parameters = array_merge($parameters, [\GuzzleHttp\RequestOptions::JSON => $data->get('json')]);
        }

        $response = $client->request($data->get('method'), $data->get('url'), $parameters);

        $body = $response->getBody()->getContents();

        if ($data->get('json')) {
            $body = json_decode($body);
        }

        $data = new Bag([
            'response' => [
                'status'   => $response->getStatusCode(),
                'time'     => microtime(true) - $time,
                'testable' => $testHandler->getRecords()[0]['message'],
                'headers' => $response->getHeaders(), 
                'body' => $body
            ],
        ]);

        $this->done($data);
    }
}
