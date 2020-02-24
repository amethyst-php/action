<?php

namespace Amethyst\Actions;

use Closure;
use Amethyst\Services\Bag;
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


        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
        ]);

        $parameters = [
            'headers' => $data->get('headers', []),
            'form_params' => $data->get('body', []),
            'query'    => $data->get('query', []),
        ];

        if ($data->get('json')) {
            $parameters = array_merge($parameters, [
                \GuzzleHttp\RequestOptions::JSON => $data->get('json', [])
            ]);
        }

        $response = $client->request($data->get('method'), $data->get('url'), $parameters);

        $body = $response->getBody()->getContents();

        if ($data->get('json')) {
            $body = json_decode($body);
        }

        $data->set('response', [
            'url' => $data->get('url'), 
            'parameters' => $parameters,
            'status'   => $response->getStatusCode(),
            'time'     => microtime(true) - $time,
            'headers' => $response->getHeaders(), 
            'body' => $body
        ]);
        
        $this->done($data);
    }
}
