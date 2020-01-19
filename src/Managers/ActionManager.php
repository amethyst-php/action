<?php

namespace Amethyst\Managers;

use Amethyst\Core\ConfigurableManager;
use Amethyst\Jobs\Action as Job;
use Amethyst\Models\Action;
use Railken\Lem\Manager;

class ActionManager extends Manager
{
    use ConfigurableManager;

    /**
     * @var string
     */
    protected $config = 'amethyst.action.data.action';

    /**
     * Dispatch a work.
     *
     * @param Action $action
     * @param array  $data
     */
    public function dispatch(Action $action, array $data = [])
    {
        $result = new Result();

        if (!$result->ok()) {
            return $result;
        }

        dispatch(new Job($action, $data));

        return $result;
    }

    /**
     * Describe extra actions.
     *
     * @return array
     */
    public function getDescriptor()
    {
        return [
            'actions' => [
                'executor',
            ],
        ];
    }
}
