<?php

return [
    // 'driver' => 'ippanel', //smsir Or ippanel
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
    ]
];
