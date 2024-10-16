<?php

namespace Bita\Message;

class SmsAdapter
{
    protected $service;

    public function __construct()
    {
        $this->service = config('bitamessage.drivers');
    }

    private function getService()
    {
        $service = $this->service[config('bitamessage.driver', 'smsir')];
        return (new $service);
    }

    public function send($message, $numbers, $api = null, $sender = null, $send_at = null)
    {
        return $this->getService()->send($message, $numbers, $api, $sender, $send_at);
    }

    public function sendByPattern($pattern, $number, $parameters, $api = null)
    {
        return $this->getService()->sendByPattern($pattern, $number, $parameters, $api);
    }

    public function checkDelivery($tracker_id)
    {
        return $this->getService()->checkDelivery($tracker_id);
    }

    public function credit()
    {
        return $this->getService()->credit();
    }

    public function getMessage($tracker_id, $api = null)
    {
        return $this->getService()->getMessage($tracker_id, $api);
    }
}
