<?php


namespace Libs;


use OSS\OssClient;
use OSS\Core\OssException;

class AliYunOss {
    private static $accessKeyId =  "LTAI4FhLaCQdzt8Xn1Fv3u33";
    private static $accessKeySecret = "bmgHisVYaFbfCzjBYJTMbimkeALk7v";
    private static $endpoint = "oss-cn-beijing.aliyuncs.com";
    private static $handler;
    private static $lastErrorMsg = "";
    private static $lastErrorCode = "";

    private static function handler()
    {
        if (!self::$handler)
        {
            try
            {
                $endpoint = "https://" . self::$endpoint;
                self::$handler = new OssClient(self::$accessKeyId, self::$accessKeySecret, $endpoint);
            } catch (OssException $e) {
                print $e->getMessage();
            }
        }

        return self::$handler;
    }

    /**
     * 文件上传
     * @param string $baseFilePath 原本文件位置
     * @param string $fileName 上传文件名
     * @param string $bucket 哪个桶
     * @return bool
     */
    public static function uploadFile($baseFilePath, $fileName, $bucket = 'subscription-oss')
    {
        try {
            self::handler()->uploadFile($bucket, $fileName, $baseFilePath);
        } catch (OssException $e) {
            self::$lastErrorCode = $e->getCode();
            self::$lastErrorMsg = $e->getMessage();
            return false;
        }
        $url = "https://" . $bucket . "." . self::$endpoint . "/" . $fileName;
        return $url;
    }

    public static function getLastError() {
        return [
            "code" => self::$lastErrorCode,
            "msg" => self::$lastErrorMsg
        ];
    }

}