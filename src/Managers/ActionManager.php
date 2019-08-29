<?php

namespace Amethyst\Managers;

use Amethyst\Common\ConfigurableManager;
use Railken\Lem\Manager;
use Amethyst\Jobs\Action as Job;
use Amethyst\Models\Action;

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
     * @param Action  $action
     * @param array $data
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
