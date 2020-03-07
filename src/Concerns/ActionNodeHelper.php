<?php

namespace Amethyst\Concerns;

use Symfony\Component\Yaml\Yaml;

trait ActionNodeHelper
{
    public function new(string $type, array $params, array $output = [], array $arguments = [])
    {
        $action = app('amethyst')->get('action')->findOrCreateOrFail([
            'name'    => $type,
            'payload' => Yaml::dump([
                'class'     => $type,
                'arguments' => [],
            ]),
        ])->getResource();

        $target = app('amethyst')->get('workflow-node')->createOrFail([
            'workflow_id' => $this->getMorphName() === 'workflow' ? $this->id : $this->workflow->id,
            'target_type' => 'action',
            'target_id'   => $action->id,
            'data'        => Yaml::dump($params),
            'output'      => Yaml::dump($output),
            'arguments'   => Yaml::dump($arguments),
        ])->getResource();

        return $target;
    }

    public function next(string $type, array $params, array $output = [], array $arguments = [])
    {
        $target = $this->new($type, $params, $output, $arguments);

        $this->relations()->attach($target);

        return $target;
    }
}
