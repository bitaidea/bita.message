<?php

namespace Bita\Message\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class SmsBaseService
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }
    public function pn2en($string)
    {
        $newNumbers = range(0, 9);
        // 1. Persian HTML decimal
        $persianDecimal = array('&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;');
        // 2. Arabic HTML decimal
        $arabicDecimal = array('&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;');
        // 3. Arabic Numeric
        $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        // 4. Persian Numeric
        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');

        $string =  str_replace($persianDecimal, $newNumbers, $string);
        $string =  str_replace($arabicDecimal, $newNumbers, $string);
        $string =  str_replace($arabic, $newNumbers, $string);
        return str_replace($persian, $newNumbers, $string);
    }

    public function DBLog($numbers, $originator, $message, $status, $service)
    {
        foreach ($numbers as $v) {
            $dataToInsert[] = [
                'message'  => $message,
                'status'   => $status,
                'from'     => $originator,
                'to'       => $v['MobileNo'],
                'tracking_code' => $v['ID'],
                'service' => $service,
                'created_at' => now()
            ];
        }
        $tableName = config('bitamessage.tableName', 'sms_logs');
        DB::table($tableName)->insert($dataToInsert);
    }
}
