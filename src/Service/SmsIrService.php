<?php

namespace Bita\Message\Service;

use Bita\Message\Contract\SmsServiceInterface;
use Bita\Message\Exception\BitaException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class SmsIrService extends SmsBaseService implements SmsServiceInterface
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
        $param = ['UserApiKey' => $this->getApiKey(), 'SecretKey' => $this->getSecretKey(), 'System' => 'laravel_v_1_4'];
        $res = Http::post($this->getEndPoint() . 'Token', $param);
        return json_decode($res->getBody(), true)['TokenKey'];
    }

    public function send($message, $numbers)
    {
        $nms = (array)$numbers;
        $numbers = [];
        foreach ($nms as $number) {
            $numbers[] = $this->pn2en($number);
        }
        $messages[] = $message;
        $param = [
            "Messages" => $messages,
            "MobileNumbers" => $numbers,
            "LineNumber" => $this->getOriginator()
        ];
        $res = Http::withHeaders(['x-sms-ir-secure-token' => $this->getToken()])->post($this->getEndPoint() . 'MessageSend', $param);
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        $this->log($res, $param);
        return $res;
    }

    public function sendByPattern($pattern, $number, $parameters)
    {
        $number = $this->pn2en($number);
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[] = ['Parameter' => $key, 'ParameterValue' => $value];
        }
        $param   = ['ParameterArray' => $params, 'TemplateId' => $pattern, 'Mobile' => $number];
        $res = Http::withHeaders(['x-sms-ir-secure-token' => $this->getToken()])->post($this->getEndPoint() . 'UltraFastSend', $param);
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        $this->log($res, $param);
        return $res;
    }

    public function checkDelivery($tracker_id)
    {
        $res = $this->getMessage($tracker_id);
        return $res;
    }

    public function credit()
    {
        $res = Http::withHeaders(['x-sms-ir-secure-token' => $this->getToken()])->get($this->getEndPoint() . 'credit');
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        return $res;
    }

    public function getMessage($tracker_id)
    {
        $res = Http::withHeaders(['x-sms-ir-secure-token' => $this->getToken()])->get($this->getEndPoint() . 'MessageSend/' . $tracker_id);
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        return $res;
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
