<?php


namespace Utils;


use Libs\AliYunOss;

class File {

    const WHITE_ETC = [ // 可以传的后缀
      "png","jpeg","gif","js","jpg","xlsx"
    ];
    private static $lastError = "";

    public static function uploadFile($temFilePath, $originFileName) {
        $etc = File::getExtensionAndCheck($originFileName);
        if (!$etc){
            self::$lastError = "非合法文件类型";
            return false;
        }
        if (!is_dir(BASE_PATH . "/public/upload/")) {
            mkdir(BASE_PATH . "/public/upload/");
        }
        $newFileName = self::getFileName($etc);
        $newFilePath = BASE_PATH . "/public/upload/" . $newFileName;
        move_uploaded_file($temFilePath, $newFilePath);
        $url = AliYunOss::uploadFile($newFilePath, $newFileName);
        if (!$url){
            self::$lastError = AliYunOss::getLastError()['msg'];
        }
        return $url;
    }

    public static function getExtensionAndCheck($filename){
        $etc = pathinfo($filename,PATHINFO_EXTENSION);
        if (!in_array($etc, self::WHITE_ETC)){
            return false;
        }
        return $etc;
    }

    public static function getFileName($etc){
        $date = date("Y-m-d-His");
        $str = Commont::randStr(6);
        return $date . "-" . $str . "." . $etc;
    }

    public static function getLastError() {
        return self::$lastError;
    }
}