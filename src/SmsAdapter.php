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

    public function send($message, $numbers)
    {
        return $this->getService()->send($message, $numbers);
    }

    public function sendByPattern($pattern, $number, $parameters)
    {
        return $this->getService()->sendByPattern($pattern, $number, $parameters);
    }

    public function checkDelivery($tracker_id)
    {
        return $this->getService()->checkDelivery($tracker_id);
    }

    public function credit()
    {
        return $this->getService()->credit();
    }
}
