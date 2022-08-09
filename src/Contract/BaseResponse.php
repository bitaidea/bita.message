<?php

namespace Bita\Message\Contract;


abstract class BaseResponse
{
    protected $data;
    // public abstract function __construct();

    public function toArray() {
        return (array)$this->data;
    }
}
