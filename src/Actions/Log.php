<?php

namespace Amethyst\Actions;

use Closure;
use Illuminate\Support\Facades\Log as Logger;
use Railken\Bag;

class Log extends Action
{
    public function requires()
    {
        return [
            'message' => 'text',
        ];
    }

    public function handle(Closure $next, Bag $data)
    {
        Logger::info($data->message);

        $next($data);
    }
}
