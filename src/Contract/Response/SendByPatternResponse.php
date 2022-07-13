<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class SendByPatternResponse extends BaseResponse
{
    public function __construct($status,$messageId,$message)
    {
        $this->data = compact('status','messageId','message');
    }
}
