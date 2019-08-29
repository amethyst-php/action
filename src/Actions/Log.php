<?php

namespace Amethyst\Actions;

use Illuminate\Support\Facades\Log as Logger;
use Amethyst\Actions\Action;
use Railken\Bag;
use Closure;

class Log extends Action
{
    public function requires()
    {
        return [
            'message' => 'text'
        ];
    }

    public function handle(Closure $next, Bag $data) 
    {
        Logger::info($data->message);

        $next($data);
    }
}