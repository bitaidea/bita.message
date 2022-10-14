<?php

namespace Bita\Message\Facades;

use Bita\Message\Contract\Response\SendByPatternResponse;
use Bita\Message\Contract\Response\SendResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method SendResponse send(string $message, array $numbers)
 * @method SendByPatternResponse sendByPattern(string $template_id, string $number, array $parameters)
 */
class Message extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'message';
    }
}
