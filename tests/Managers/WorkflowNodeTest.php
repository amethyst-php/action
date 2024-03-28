<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\WorkflowNodeFaker;
use Amethyst\Managers\WorkflowNodeManager;
use Amethyst\Tests\Base;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class WorkflowNodeTest extends Base
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = WorkflowNodeManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = WorkflowNodeFaker::class;
}
