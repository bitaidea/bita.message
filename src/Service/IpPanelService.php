<?php

namespace Bita\Message\Service;

use Bita\Message\Contract\SmsServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class IpPanelService extends SmsBaseService implements SmsServiceInterface
{
    public static function log($res, $param)
    {
        $tracker_id = $res['data']['bulk_id'];
        if (isset($param['pattern_code'])) {
            $message = self::getMessage($tracker_id);
            $message = $message['data']['message']['message'];
            $numbers[] = ["ID" => $tracker_id, "MobileNo" => $param['recipient']];
        } else {
            $numbers = [];
            foreach ($param['recipients'] as $number) {
                array_push($numbers, ["ID" => $tracker_id, "MobileNo" => $number]);
            }
            $message = $param['message'];
        }
        $status = $res['status'] == "OK" ? 1 : 0;
        self::DBLog($numbers, self::getOriginator(), $message, $status, self::getServiceName());
    }

    public static function send($message, $numbers)
    {
        $nms = (array)$numbers;
        $numbers = [];
        foreach ($nms as $number) {
            $numbers[] = self::pn2en($number);
        }
        $param = [
            "originator" => self::getOriginator(),
            "recipients" => $numbers,
            "message" => $message
        ];
        $res = Http::withHeaders(self::getHeader())->post(self::getEndPoint() . 'messages', $param);
        self::log($res, $param);
        return $res;
    }

    public static function sendByPattern($pattern, $number, $parameters)
    {
        $number = self::pn2en($number);
        $param = [
            "pattern_code" => $pattern,
            "originator" => self::getOriginator(),
            "recipient" => $number,
            "values" => $parameters
        ];
        $res = Http::withHeaders(self::getHeader())->post(self::getEndPoint() . 'messages/patterns/send', $param);
        self::log($res, $param);
        return $res;
    }

    public static function checkDelivery($tracker_id)
    {
        $res = Http::withHeaders(self::getHeader())->get(self::getEndPoint() . "messages/$tracker_id/recipients");
        return $res;
    }

    public static function credit()
    {
        $res = Http::withHeaders(self::getHeader())->get(self::getEndPoint() . 'credit');
        return $res;
    }

    public static function getMessage($tracker_id)
    {
        $res = Http::withHeaders(self::getHeader())->get(self::getEndPoint() . 'messages/' . $tracker_id);
        return $res;
    }

    public static function getEndPoint()
    {
        return Config::get('bitamessage.ipPanel')['endPoint'];
    }

    public static function getOriginator()
    {
        return Config::get('bitamessage.ipPanel')['originator'];
    }

    public static function getApiKey()
    {
        return Config::get('bitamessage.ipPanel')['apiKey'];
    }

    public static function getServiceName()
    {
        return Config::get('bitamessage.ipPanel')['name'];
    }
     
    public static function getHeader()
    {
        return [
            'Authorization' => 'AccessKey ' . self::getApiKey(),
            'Content-Type' => 'application/json'
        ];
    }
}
