<?php

namespace Bita\Message\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Message
 * @package Bita\Message\Facades
 *
 * @method static string sendByPattern(string $template_id,int $number,array $parameters)
 * @method static string send(string $message, array $numbers)
 */
class Message extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'message';
    }
}
