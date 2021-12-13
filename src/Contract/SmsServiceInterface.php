<?php

namespace Bita\Message\Contract;


interface SmsServiceInterface
{
    public static function send($message, $numbers);
    public static function sendByPattern($pattern, $number, $parameters);
    public static function checkDelivery($tracker_id);
    public static function credit();
}
