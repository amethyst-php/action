<?php

namespace Amethyst\Actions;

use Amethyst\Actions\Action;
use Illuminate\Support\Facades\Event;
use Railken\Bag;
use Closure;

class Listener extends Action
{
	public function handle(Closure $next, Bag $data)
    {
        Event::listen([$this->data->event], function ($event_name, $events) use ($next, $data) {

        	print_r('Yolo');
        	die();
        	
            $next($data->merge(new Bag($events[0]->getData())));
        });
    }
}