<?php

namespace Bita\Message\Service;

use Bita\Message\Contract\SmsServiceInterface;
use Bita\Message\Exception\BitaException;
use Illuminate\Support\Facades\Config;

class TestService extends SmsBaseService implements SmsServiceInterface
{
    public function log($res, $param)
    {
        if (!Config::get('bitamessage.logs')) return;
        else {
            if (isset($param['TemplateId'])) {
                $tracker_id = (int)$res['VerificationCodeId'];
                $message = $this->getMessage($tracker_id);
                $message = $message['Messages']['SMSMessageBody'];
                $numbers[] = ["ID" => $tracker_id, "MobileNo" => $param['Mobile']];
            } else {
                $numbers = $res['Ids'];
                $message = $param['Messages'][0];
            }
            $status = $res['IsSuccessful'] == "true" ? 1 : 0;
            $this->DBLog($numbers, $this->getOriginator(), $message, $status, $this->getServiceName());
        }
    }

    public function getToken()
    {

    }

    public function send($message, $numbers)
    {
        return  [
            "VerificationCodeId" => 290737339.0,
            "IsSuccessful" => true,
            "Message" => "your verification code is sent"
          ];

    }

    public function sendByPattern($pattern, $number, $parameters)
    {
        return  [
            "VerificationCodeId" => 290737339.0,
            "IsSuccessful" => true,
            "Message" => "your verification code is sent"
          ];

    }

    public function checkDelivery($tracker_id)
    {
    }

    public function credit()
    {
      
    }

    public function getMessage($tracker_id)
    {
      
    }

    public function getEndPoint()
    {
        return Config::get('bitamessage.smsIr')['endPoint'];
    }

    public function getOriginator()
    {
        return Config::get('bitamessage.smsIr')['originator'];
    }

    public function getApiKey()
    {
        return Config::get('bitamessage.smsIr')['apiKey'];
    }

    public function getSecretKey()
    {
        return Config::get('bitamessage.smsIr')['secretKey'];
    }

    public function getServiceName()
    {
        return Config::get('bitamessage.smsIr')['name'];
    }

    public function getException($res)
    {
        if (isset($res['IsSuccessful']) && $res['IsSuccessful'] != "true") {
            $error = isset($res['Message']) ? $res['Message'] : 'خظا';
            throw new BitaException($error);
        } else return;
    }
}
