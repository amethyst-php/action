<?php

namespace Amethyst\Http\Controllers\Admin;

use Amethyst\Api\Http\Controllers\RestManagerController;
use Amethyst\Api\Http\Controllers\Traits as RestTraits;
use Amethyst\Managers\ActionManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionsController extends RestManagerController
{
    use RestTraits\RestCommonTrait;

    /**
     * The class of the manager.
     *
     * @var string
     */
    public $class = ActionManager::class;

    /**
     * @param int                      $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(int $id, Request $request)
    {
        /** @var \Amethyst\Managers\ActionManager */
        $manager = $this->manager;

        /** @var \Amethyst\Models\Action */
        $action = $manager->getRepository()->findOneById($id);

        if ($action == null) {
            return $this->response('', Response::HTTP_NOT_FOUND);
        }

        $result = $manager->execute($action, (array) $request->input('data'));

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        return $this->success([]);
    }
}
