<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class SendByPatternResponse extends BaseResponse
{
    public function __construct($status, $messageId, $message, $cost = 0)
    {
        $this->data = compact('status', 'messageId', 'message', 'cost');
    }
}
