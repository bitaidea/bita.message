<?php

namespace Bita\Message\Service;

use Bita\Management\Services\SystemStatus;
use Bita\Message\Contract\Response\GetCreditResponse;
use Bita\Message\Contract\Response\SendByPatternResponse;
use Bita\Message\Contract\Response\SendResponse;
use Bita\Message\Contract\SmsServiceInterface;
use Bita\Message\Events\SendMessage;
use Bita\Message\Exception\BitaException;
use Bita\Message\Service\SmsBaseService;
use Bita\Notification\Models\SmsLog;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KavenegarService extends SmsBaseService implements SmsServiceInterface
{
    protected $key = "kavenegar";

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->getEndPoint(),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
    }

    public function log($res, $param)
    {
        if (!Config::get('bitamessage.logs')) return;

        $numbers = [];
        if (isset($res['entries']) && is_array($res['entries']))
            foreach ($res['entries'] as $number)
                $numbers[] = ['MobileNo' => $number['receptor'], 'ID' => $number['messageid']];
        elseif ($param['receptor'])
            foreach ($param['receptor'] as $number)
                $numbers[] = ['MobileNo' => $number['mobile'], 'ID' => $number['id']];
        $message = 'خطا';

        if (isset($res['message']))
            $message = $res['message'];
        elseif (isset($res['entries'][0]))
            $message = $res['entries'][0]['message'];

        $this->DBLog($numbers, $this->getNumber(), $message, 0, Config::get('bitamessage.kavenegar')['name']);
    }

    public function checkDelivery($tracker_id)
    {
        $key = $this->getToken();
        $result = $this->client->post("$key/sms/status.json?messageid={$tracker_id}");
        $res = json_decode($result->getBody(), true);
        $this->getException($res);
        return $res['entries'];
    }



    /**
     * Simple send message with sms.ir account and line number
     *
     * @param $message
     * @param $numbers  = Numbers - must be equal with $messages
     * @param null $sendDateTime = don't fill it if you want to send message now
     *
     * @return mixed, return status
     */
    public function send($message, $numbers)
    {
        $nms = (array)$numbers;
        $numbers = null;
        foreach ($nms as $k => $number) {
            $numbers .= $this->pn2en($number);
            if ($k + 1 < count($nms))
                $numbers .= ",";
        }
        $key = $this->getToken();
        $body   = ['message' => $message, 'receptor' => $numbers, 'sender' => $this->getNumber()];
        $result     = $this->client->post("$key/sms/send.json", ['form_params' => $body]);

        $res = json_decode($result->getBody(), true);
        $this->log($res, $res['entries']);

        $sendResponse = null;

        foreach ($res['entries'] as $entity) {
            $sendResponse = new SendResponse(!!$res['return']['status'], $entity['messageid'], $entity['message']);
            event(new SendMessage($sendResponse));
        }

        return $sendResponse->toArray();
    }

    /**
     * @param array $parameters = all parameters and parameters value as an array
     * @param $template_id = you must create a template in sms.ir and put your template id here
     * @param $number = phone number
     * @return mixed = the result
     */

    public function sendByPattern($template_id, $number, $parameters)
    {
        $hasToken = Str::startsWith(array_keys($parameters)[0], 'token');
        $tokens = array_values($parameters);
        $qs = '';

        if ($hasToken)
            foreach ($parameters as $k => $v) {
                $qs .= "&$k=$v";
            }
        else
            foreach ($tokens as $k => $v) {
                $val = str_replace(' ', '.', $v);

                if ($k == 0)
                    $qs .= "&token=$val";
                else {
                    $index = intval($k) + 1;
                    $qs .= "&token$index=$val";
                }
            }

        $key = $this->getToken();
        $result = $this->client->get("$key/verify/lookup.json?receptor=$number&template=$template_id" . $qs);

        $res = json_decode($result->getBody(), true);
        $entries = $res['entries'][0];

        $this->log($entries, ['receptor' => [[
            'mobile' => $number, 'id' => $entries['messageid']
        ]]]);

        $sendByPatternResponse = new SendByPatternResponse($res['return']['status'] == 200, $entries['messageid'], $res['return']['message'], $entries['cost']);
        event(new SendMessage($sendByPatternResponse, $template_id));

        return $sendByPatternResponse->toArray();
    }

    /**
     * this method return your credit in sms.ir (sms credit, not money)
     *
     * @return mixed - credit
     */
    public function credit()
    {
        $key = $this->getToken();
        $result = Http::get("$key/account/info.json");
        $res = json_decode($result->getBody(), true);
        $credit = $res['entries']['remaincredit'];
        return (new GetCreditResponse($credit))->toArray();
    }

    private  function getNumber()
    {
        return  Config::get('bitamessage.kavenegar')['originator'];
    }

    /**
     * this method used in every request to get the token at first.
     *
     * @return mixed - the Token for use api
     *
     */
    public  function getToken()
    {
        return Config::get('bitamessage.kavenegar')['apiKey'];
    }


    public function getEndPoint()
    {
        return Config::get('bitamessage.kavenegar')['endPoint'];
    }

    public function getException($res)
    {
        if (isset($res['return']['status']) && $res['return']['status'] != 200) {
            $error = isset($res['return']['message']) ? $res['return']['message'] : 'خظا';
            throw new BitaException($error);
        } else return;
    }
}
