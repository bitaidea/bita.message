<?php

namespace Bita\Message\Service;

use Bita\Message\Contract\Response\GetCreditResponse;
use Bita\Message\Contract\Response\SendByPatternResponse;
use Bita\Message\Contract\Response\SendResponse;
use Bita\Message\Contract\SmsServiceInterface;
use Bita\Message\Exception\BitaException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class SmsIrV2Service extends SmsBaseService implements SmsServiceInterface
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
            $this->DBLog($numbers, $this->getOriginator(), $message, $status, 'smsIRV2');
        }
    }

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->getEndPoint(),
            'headers' => [
                'X-API-KEY' => $this->getApiKey(),
                'ACCEPT' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function send($message, $numbers, $api = null, $sender = null)
    {
        $nms = (array)$numbers;
        $numbers = [];
        foreach ($nms as $number) {
            $numbers[] = $this->pn2en($number);
        }

        $param = [
            "MessageText" => $message,
            "Mobiles" => $numbers,
            "lineNumber" => $this->getOriginator()
        ];
        $res = $this->client->post('send/bulk', ['body' => json_encode($param)]);
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        return (new SendResponse($res['status'], $res['data']['packId'], $res['message'], $res['data']['cost']))->toArray();
    }

    public function sendByPattern($pattern, $number, $parameters, $api = null)
    {
        $number = $this->pn2en($number);
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[] = ['Name' => $key, 'Value' => "{$value}"];
        }
        $param   = ['Parameters' => $params, 'TemplateId' => (int)$pattern, 'Mobile' => $number];
        $res = $this->client->post('send/verify', ['body' => json_encode($param)]);

        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        $this->log($res, $param);
        return (new SendByPatternResponse($res['status'], $res['data']['messageId'], $res['message'], $res['data']['cost']))->toArray();
    }

    public function checkDelivery($tracker_id)
    {
        $res = $this->getMessage($tracker_id);
        return $res;
    }

    public function credit()
    {
        $res = $this->client->get('credit');
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        return (new GetCreditResponse($res['data']))->toArray();
    }

    public function getMessage($tracker_id)
    {
        $res = $this->client->get($this->getEndPoint() . 'send' . $tracker_id, ['headers' => ['X-API-KEY' => $this->getApiKey()]]);
        $res = json_decode($res->getBody()->getContents(), true);
        $this->getException($res);
        return $res;
    }

    public function getEndPoint()
    {
        return Config::get('bitamessage.smsIrV2')['endPoint'];
    }

    public function getOriginator()
    {
        return Config::get('bitamessage.smsIrV2')['originator'];
    }

    public function getApiKey()
    {
        return Config::get('bitamessage.smsIrV2')['apiKey'];
    }


    public function getException($res)
    {
        if (isset($res['IsSuccessful']) && $res['IsSuccessful'] != "true") {
            $error = isset($res['Message']) ? $res['Message'] : 'خظا';
            throw new BitaException($error);
        } else return;
    }
}
