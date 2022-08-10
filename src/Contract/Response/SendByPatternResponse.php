<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;
use Bita\Message\Events\SendMessage;

class SendByPatternResponse extends BaseResponse
{
    public function __construct($status, $messageId, $message, $cost = 0)
    {
        $this->data = compact('status', 'messageId', 'message', 'cost');
    }

    public function event()
    {
        event(new SendMessage($this));
        return $this;
    }
}
