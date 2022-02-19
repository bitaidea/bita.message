<?php

use Bita\Message\Service\IpPanelService;
use Bita\Message\Service\SmsIrService;

return [
    'driver' => 'ippanel', //default service is smsir 
    'ipPanel' => [
        'name' => 'IpPanel',
        'endPoint' => env('BITA_MESSAGE_IP_PANEL_ENDPOINT'),
        'originator' => env('BITA_MESSAGE_IP_PANEL_ORIGINATOR'),
        'apiKey' => env('BITA_MESSAGE_IP_PAENL_API_KEY')
    ],
    'smsIr' => [
        'name' => 'SmsIr',
        'endPoint' => env('BITA_MESSAGE_SMS_IR_ENDPOINT'),
        'originator' => env('BITA_MESSAGE_SMS_IR_ORIGINATOR'),
        'apiKey' => env('BITA_MESSAGE_SMS_IR_API_KEY'),
        'secretKey' => env('BITA_MESSAGE_SMS_IR_SECRET_KEY')
    ],
    'logs' => false,
    'tableName' => 'sms_logs', //default name is sms_logs
    //you can add driver here and set the key in driver
    'drivers' => [
        'ippanel' => IpPanelService::class,
        'smsir' => SmsIrService::class,
        'test' => TestService::class
    ]
];
