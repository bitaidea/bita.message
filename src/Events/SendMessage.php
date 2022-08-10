<?php

namespace Bita\Message\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $response;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($response)
    {
        $this->response = $response;
    }
}
