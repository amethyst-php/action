<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\WorkflowStateFaker;
use Amethyst\Managers\WorkflowStateManager;
use Amethyst\Tests\BaseTest;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class WorkflowStateTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = WorkflowStateManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = WorkflowStateFaker::class;
}
