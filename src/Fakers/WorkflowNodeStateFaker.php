<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Railken\Lem\Faker;

class WorkflowNodeStateFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('workflow_node', WorkflowNodeFaker::make()->parameters()->toArray());
        $bag->set('workflow_state', WorkflowStateFaker::make()->parameters()->toArray());
        $bag->set('state', 'wait');
        $bag->set('data', serialize(['x' => 1]));
        $bag->set('input', serialize(['x' => 1]));
        $bag->set('output', serialize(['x' => 1]));

        return $bag;
    }
}
