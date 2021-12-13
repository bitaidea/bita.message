<?php

namespace Bita\Service;

use Bita\Contract\SmsServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class SmsIrService extends SmsBaseService implements SmsServiceInterface
{
    public static function log($res, $param)
    {
        $res = json_decode($res->getBody()->getContents(), true);
        if (isset($param['TemplateId'])) {
            $tracker_id = (int)$res['VerificationCodeId'];
            $message = self::getMessage($tracker_id);
            $message = $message['Messages']['SMSMessageBody'];
            $numbers[] = ["ID" => $tracker_id, "MobileNo" => $param['Mobile']];
        } else {
            $numbers = $res['Ids'];
            $message = $param['Messages'][0];
        }
        $status = $res['IsSuccessful'] == "true" ? 1 : 0;
        self::DBLog($numbers, self::getOriginator(), $message, $status, self::getServiceName());
    }

    public static function getToken()
    {
        $param = ['UserApiKey' => self::getApiKey(), 'SecretKey' => self::getSecretKey(), 'System' => 'laravel_v_1_4'];
        $res = Http::post(self::getEndPoint() . 'Token', $param);
        return json_decode($res->getBody(), true)['TokenKey'];
    }

    public static function send($message, $numbers)
    {
        $nms = (array)$numbers;
        $numbers = [];
        foreach ($nms as $number) {
            $numbers[] = self::pn2en($number);
        }
        $messages[] = $message;
        $param = [
            "Messages" => $messages,
            "MobileNumbers" => $numbers,
            "LineNumber" => self::getOriginator()
        ];
        $res = Http::withHeaders(['x-sms-ir-secure-token' => self::getToken()])->post(self::getEndPoint() . 'MessageSend', $param);
        self::log($res, $param);
        return $res;
    }

    public static function sendByPattern($pattern, $number, $parameters)
    {
        $number = self::pn2en($number);
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[] = ['Parameter' => $key, 'ParameterValue' => $value];
        }
        $param   = ['ParameterArray' => $params, 'TemplateId' => $pattern, 'Mobile' => $number];
        $res = Http::withHeaders(['x-sms-ir-secure-token' => self::getToken()])->post(self::getEndPoint() . 'UltraFastSend', $param);
        self::log($res, $param);
        return $res;
    }

    public static function checkDelivery($tracker_id)
    {
        $res = self::getMessage($tracker_id);
        return $res;
    }

    public static function credit()
    {
        $res = Http::withHeaders(['x-sms-ir-secure-token' => self::getToken()])->get(self::getEndPoint() . 'credit');
        return $res;
    }

    public static function getMessage($tracker_id)
    {
        $res = Http::withHeaders(['x-sms-ir-secure-token' => self::getToken()])->get(self::getEndPoint() . 'MessageSend/' . $tracker_id);
        return json_decode($res->getBody(), true);
    }

    public static function getEndPoint()
    {
        return Config::get('bitamessage.smsIr')['endPoint'];
    }

    public static function getOriginator()
    {
        return Config::get('bitamessage.smsIr')['originator'];
    }
    public static function getApiKey()
    {
        return Config::get('bitamessage.smsIr')['apiKey'];
    }

    public static function getSecretKey()
    {
        return Config::get('bitamessage.smsIr')['secretKey'];
    }

    public static function getServiceName()
    {
        return Config::get('bitamessage.smsIr')['name'];
    }
}
