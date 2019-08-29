<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Railken\Lem\Faker;
use Symfony\Component\Yaml\Yaml;

class WorkflowNodeFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('workflow', WorkflowFaker::make()->parameters()->toArray());
        $bag->set('target_type', 'action');
        $bag->set('target', ActionFaker::make()->parameters()->toArray());
        $bag->set('data', Yaml::dump(['dummy']));

        return $bag;
    }
}
