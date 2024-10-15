<?php

namespace Bita\Message\Service;

use Bita\Message\Contract\SmsServiceInterface;
use Bita\Message\Exception\BitaException;
use Illuminate\Support\Facades\Config;

class IpPanelService extends SmsBaseService implements SmsServiceInterface
{
    public function log($res, $param)
    {
        if (!Config::get('bitamessage.logs')) return;
        else {
            $tracker_id = $res['data']['bulk_id'];
            if (isset($param['pattern_code'])) {
                $message = $this->getMessage($tracker_id);
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
            $this->DBLog($numbers, $this->getOriginator(), $message, $status, $this->getServiceName());
        }
    }

    public function send($message, $numbers, $api = null, $sender = null)
    {
        $nms = (array)$numbers;
        $numbers = [];
        foreach ($nms as $number) {
            $numbers[] = $this->pn2en($number);
        }
        $param = [
            "originator" => $this->getOriginator(),
            "recipients" => $numbers,
            "message" => $message
        ];
        $res = $this->client->post($this->getEndPoint() . 'messages', ['json' => $param, 'headers' => $this->getHeader()]);
        $res = json_decode($res->getBody()->getContents(), true);
        if (!isset($res['status']) || $res['status'] != "OK") {
            $error = isset($res['data']) && isset($res['data']['error']) ? $res['data']['error'] : 'مشکل در ارسال پیام';
            throw new BitaException($error);
        }
        $this->log($res, $param);
        return $res;
    }

    public function sendByPattern($pattern, $number, $parameters, $api = null)
    {
        $number = $this->pn2en($number);
        $param = [
            "pattern_code" => $pattern,
            "originator" => $this->getOriginator(),
            "recipient" => $number,
            "values" => $parameters
        ];
        $res = $this->client->post($this->getEndPoint() . 'messages/patterns/send', ['json' => $param, 'headers' => $this->getHeader()]);
        $res = json_decode($res->getBody()->getContents(), true);
        if (!isset($res['data']) || !isset($res['data']['bulk_id']) || $res['data']['bulk_id'] == 0) {
            throw new BitaException('مشکل در ارسال پیام');
        }
        $this->log($res, $param);
        return $res;
    }

    public function checkDelivery($tracker_id)
    {
        $res = $this->client->get($this->getEndPoint() . "messages/$tracker_id/recipients", ['headers' => $this->getHeader()]);
        $res = json_decode($res->getBody()->getContents(), true);
        if (!isset($res['data']) || !isset($res['data']['recipients']) || empty($res['data']['recipients'])) {
            throw new BitaException('کد بالک اشتباه است');
        }
        return $res;
    }

    public function credit()
    {
        $res = $this->client->get($this->getEndPoint() . 'credit', ['headers' => $this->getHeader()]);
        $res = json_decode($res->getBody()->getContents(), true);
        if (isset($res['status']) && $res['status'] != "OK") {
            throw new BitaException('خطا');
        }
        return $res;
    }

    public function getMessage($tracker_id)
    {
        $res = $this->client->get($this->getEndPoint() . 'messages/' . $tracker_id, ['headers' => $this->getHeader()]);
        $res = json_decode($res->getBody()->getContents(), true);
        if (isset($res['status']) && $res['status'] != "OK") {
            throw new BitaException('خطا');
        }
        return $res;
    }

    public function getEndPoint()
    {
        return Config::get('bitamessage.ipPanel')['endPoint'];
    }

    public function getOriginator()
    {
        return Config::get('bitamessage.ipPanel')['originator'];
    }

    public function getApiKey()
    {
        return Config::get('bitamessage.ipPanel')['apiKey'];
    }

    public function getServiceName()
    {
        return Config::get('bitamessage.ipPanel')['name'];
    }

    public function getHeader()
    {
        return [
            'Authorization' => 'AccessKey ' . $this->getApiKey(),
            'Content-Type' => 'application/json'
        ];
    }
}
