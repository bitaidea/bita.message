<?php

namespace Bita\Message;

use Bita\Message\Service\IpPanelService;
use Bita\Message\Service\SmsIrService;

class SmsAdapter
{
    protected $service;

    public function __construct()
    {
        $this->service = [
            'smsir' => SmsIrService::class,
            'ippanel' => IpPanelService::class
        ];
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