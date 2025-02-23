<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class GetCreditResponse extends BaseResponse
{
    public function __construct($credit)
    {
        $this->data = compact('credit');
    }
}
