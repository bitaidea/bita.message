<?php

namespace Bita\Message\Contract\Response;

use Bita\Message\Contract\BaseResponse;

class GetCreditResponse extends BaseResponse
{
    public function __construct(bool $credit)
    {
        $this->data = compact('credit');
    }
}
