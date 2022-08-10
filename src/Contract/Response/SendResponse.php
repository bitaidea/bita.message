<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class SendResponse extends BaseResponse
{
    public function __construct(bool $status, $packId, string $message, $cost = 0)
    {
        $this->data = compact('status', 'packId', 'message', 'cost');
    }
}
