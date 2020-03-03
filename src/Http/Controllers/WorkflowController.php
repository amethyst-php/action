<?php

namespace Amethyst\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Railken\LaraEye\Exceptions\FilterSyntaxException;
use Railken\Lem\Result;
use Symfony\Component\HttpFoundation\Response;
use Amethyst\Core\Http\Controllers\RestManagerController;

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

        app('amethyst.action')->dispatchByWorkflow($query->first());
        
        return $this->response([], Response::HTTP_OK);
    }
}
