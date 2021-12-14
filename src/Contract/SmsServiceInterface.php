<?php

namespace Bita\Message\Contract;


interface SmsServiceInterface
{
    public function send($message, $numbers);
    public function sendByPattern($pattern, $number, $parameters);
    public function checkDelivery($tracker_id);
    public function credit();
}
