<?php

namespace Amethyst\Http\Controllers;

use Amethyst\Core\Http\Controllers\RestManagerController;
use Amethyst\Jobs\DispatchWorkflow;
use Illuminate\Http\Request;
use Railken\LaraEye\Exceptions\FilterSyntaxException;
use Symfony\Component\HttpFoundation\Response;

class WorkflowController extends RestManagerController
{
    public function __construct()
    {
        $this->manager = app('amethyst')->get('workflow');
    }

    /**
     * Display current user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(Request $request)
    {
        $query = $this->getQuery();

        try {
            $this->filterQuery($query, $request);
        } catch (FilterSyntaxException $e) {
            return $this->error(['code' => 'QUERY_SYNTAX_ERROR', 'message' => $e->getMessage()]);
        }

        $workflow = $query->first();

        $data = (array) $request->input('data', []);

        $data['__agent'] = $this->getUser();

        $job = new DispatchWorkflow($workflow, $data);

        if ($request->input('queue', 0)) {
            dispatch($job);
        } else {
            $job->handle();
        }

        return $this->response([], Response::HTTP_OK);
    }
}
