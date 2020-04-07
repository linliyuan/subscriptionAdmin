<?php


namespace Utils;


use Phalcon\Di;

class easyWechat {
    /**
     * 获取easyWechat需要的配置
     * @param $appId
     * @return array|mixed
     */
    public static function getEasyWechatMinConfig($appId){
        $config = Di::getDefault()->get("config");
        $config = [
            'app_id' => $appId,
            'secret' => $config->app->secret, // 查库获取 secret

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => BASE_PATH . '/public/logs/wechat/' . date('Y-m-d') . ".log",
            ],
        ];
        return $config;
    }
}