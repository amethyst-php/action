<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\ActionFaker;
use Amethyst\Managers\ActionManager;
use Amethyst\Tests\Base;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class ActionTest extends Base
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = ActionManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = ActionFaker::class;
}
