<?php
namespace Utils;

use GuzzleHttp\Client;

class Reporter
{
    const LOGIN_FAIL = "http://blackhole.heywoodsminiprogram.com/report/Ya86_INZz/1";

    const REDIS_LOCK_FAIL = "http://blackhole.heywoodsminiprogram.com/report/Lo4gXINZk/1";

    const USER_NOT_FOUND = "http://blackhole.heywoodsminiprogram.com/report/cs-RuSNZk/1";

    const ALI_PIC_SCAN_FAIL = "http://blackhole.heywoodsminiprogram.com/report/cs-RuSNZk/1";

    /**
     * 发送报警
     * @param $url
     * @param $appId
     * @param $arr
     */
    public static function send($url,$appId,$arr)
    {
        $config = \Phalcon\Di::getDefault()->getConfig();
        $client = new Client();
        if ($config->application->env == "prod"){
            $client->get($url,[
                'query' => [
                    'appid' => $appId,
                ]
            ]);
        }
    }
}