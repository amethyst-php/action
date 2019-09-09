<?php

namespace Amethyst\Actions;

use Closure;
use Railken\Bag;

class Action
{
    protected $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function dispatch(Closure $next, Bag $data)
    {
        dispatch(new \Amethyst\Jobs\Action($this, $next, $data));
    }
}
