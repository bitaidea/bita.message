<?php

namespace Bita\Message\Contract;


interface SmsServiceInterface
{
    public function send($message, $numbers, $api = null, $sender = null, $send_at = null);
    public function sendByPattern($pattern, $number, $parameters, $api = null);
    public function checkDelivery($tracker_id);
    public function getMessage($tracker_id, $api = null);
    public function credit();
}
