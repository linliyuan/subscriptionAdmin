<?php


namespace Services;


use EasyWeChat\Factory;
use Libs\Guzzle;
use Utils\easyWechat;

class SubscribeService {
    public static function sendSubscribeMsg($appId, $touser, $msg_id, $sendTime, $sender, $thing="") {
        $config = easyWechat::getEasyWechatMinConfig($appId);
        $data   = [
            'template_id' => 'z-raFxtQPN04ZtEGrOn2_rhy5QVZ3qng3yM3gm1koIQ', // 所需下发的订阅模板id
            'touser'      => $touser,     // 接收者（用户）的 openid
            'page'        => 'msg?msg_id=' . $msg_id,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
            'data'        => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
                'time2'  => [
                    'value' => $sendTime,
                ],
                'thing3' => [
                    'value' => "您有新消息未读，请点击查看",
                ],
                'name1'  => [
                    'value' => $sender,
                ],
                'thing4' => [
                    'value' => $thing,
                ],
            ],
        ];

        $app = Factory::miniProgram($config);
        $res = $app->subscribe_message->send($data);
        var_dump($res);
    }
}