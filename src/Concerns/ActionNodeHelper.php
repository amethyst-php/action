<?php

namespace Amethyst\Concerns;

use Symfony\Component\Yaml\Yaml;

trait ActionNodeHelper
{
	public function next(string $type, array $params)
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
        ])->getResource();

        $this->relations()->attach($target);
        
        return $target;
	}
}