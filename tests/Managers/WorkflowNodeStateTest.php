<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\WorkflowNodeStateFaker;
use Amethyst\Managers\WorkflowNodeStateManager;
use Amethyst\Tests\BaseTest;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class WorkflowNodeStateTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = WorkflowNodeStateManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = WorkflowNodeStateFaker::class;
}
