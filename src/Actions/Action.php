<?php

namespace Amethyst\Actions;

use Railken\Bag;
use Closure;

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