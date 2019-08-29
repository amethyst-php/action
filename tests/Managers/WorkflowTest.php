<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\WorkflowFaker;
use Amethyst\Managers\WorkflowManager;
use Amethyst\Tests\BaseTest;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class WorkflowTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = WorkflowManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = WorkflowFaker::class;
}
