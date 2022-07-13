<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class SendResponse extends BaseResponse
{
    public function __construct(bool $status, $packId, string $message)
    {
        $this->data = compact('status', 'packId', 'message');
    }
}
