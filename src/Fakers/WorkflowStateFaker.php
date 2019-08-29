<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Railken\Lem\Faker;

class WorkflowStateFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('workflow', WorkflowFaker::make()->parameters()->toArray());
        $bag->set('state', 'running');

        return $bag;
    }
}
