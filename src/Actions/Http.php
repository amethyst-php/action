<?php

namespace Amethyst\Actions;

use Amethyst\Models\WorkflowNode;
use Amethyst\Models\WorkflowNodeState;
use Amethyst\Services\Bag;

class Http extends Action
{
    public function handle(Bag $data, WorkflowNode $workflowNode, WorkflowNodeState $nodeState = null)
    {
        $time = microtime(true);

        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
        ]);

        $parameters = [
            'headers'     => $data->get('headers', []),
            'form_params' => $data->get('body', []),
            'query'       => $data->get('query', []),
        ];

        if ($data->get('json')) {
            $parameters = array_merge($parameters, [
                \GuzzleHttp\RequestOptions::JSON => $data->get('json', []),
            ]);
        }

        $response = $client->request($data->get('method'), $data->get('url'), $parameters);

        $body = $response->getBody()->getContents();

        if ($data->get('json')) {
            $body = json_decode($body);
        }

        $data->set('response', [
            'url'        => $data->get('url'),
            'parameters' => $parameters,
            'status'     => $response->getStatusCode(),
            'time'       => microtime(true) - $time,
            'headers'    => $response->getHeaders(),
            'body'       => $body,
        ]);

        \Log::info('Http Request: '.json_encode($data->get('response')));

        $this->done($data);
    }
}
