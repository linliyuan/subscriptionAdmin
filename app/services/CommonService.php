<?php


namespace Services;


use Utils\File;

class CommonService {
    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 最多50M
    private static $lastError = ""; // 错误登记

    public static function uploadFileService($files){
        if ($files['error'] != 0){
            self::setError("文本传输错误");
            return false;
        }
        if ($files['size'] > self::MAX_FILE_SIZE){
            self::setError("文本过大");
            return false;
        }
        $res = File::uploadFile($files['tmp_name'], $files['name']);
        if (!$res) {
            self::setError(File::getLastError());
            return false;
        }
        return $res;
    }

    private static function setError($error){
        self::$lastError = $error;
    }

    public static function getError(){
        return self::$lastError;
    }
}