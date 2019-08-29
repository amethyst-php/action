<?php

namespace Amethyst\Tests;

class DummyEvent
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getData()
    {
        return [
            'message' => $this->message
        ];
    }
}