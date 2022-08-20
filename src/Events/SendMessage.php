<?php

namespace Bita\Message\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage
{
    use Dispatchable, SerializesModels;

    public $response;
    public $pattern;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($response, $pattern = '')
    {
        $this->response = $response;
        $this->pattern = $pattern;
    }
}
