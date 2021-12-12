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

    public function send($message, $numbers)
    {
        $service = $this->service[config('bitamessage.driver', 'smsir')];
        return (new $service)->send($message, $numbers);
    }

    public function sendByPattern($pattern, $number, $parameters)
    {
        $service = $this->service[config('bitamessage.driver', 'smsir')];
        return (new $service)->sendByPattern($pattern, $number, $parameters);
    }

    public function checkDelivery($tracker_id)
    {
        $service = $this->service[config('bitamessage.driver', 'smsir')];
        return (new $service)->checkDelivery($tracker_id);
    }

    public function credit()
    {
        $service = $this->service[config('bitamessage.driver', 'smsir')];
        return (new $service)->credit();
    }
}
