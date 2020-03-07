<?php

namespace Amethyst\Managers;

use Amethyst\Core\ConfigurableManager;
use Amethyst\Jobs\Action as Job;
use Amethyst\Models\Action;
use Railken\Lem\Manager;
use Railken\Lem\Result;

class ActionManager extends Manager
{
    use ConfigurableManager;

    /**
     * @var string
     */
    protected $config = 'amethyst.action.data.action';

    /**
     * Dispatch a work.
     */
    public function dispatch(Action $action, array $data = [])
    {
        $result = new Result();

        if (!$result->ok()) {
            return $result;
        }

        dispatch(new Job($action, function () { }, $data));

        return $result;
    }
}
