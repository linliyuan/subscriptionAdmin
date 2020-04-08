<?php


namespace Utils;


class File {

    const WHITE_ETC = [ // 可以传的后缀
      "png","jpeg","gif","js"
    ];

    public static function uploadFile($temFilePath, $newFileName) {
        if (!is_dir(BASE_PATH . "/public/upload/")) {
            mkdir(BASE_PATH . "/public/upload/");
        }
        move_uploaded_file($temFilePath, BASE_PATH . "/public/upload/" . $newFileName);
    }

    public static function getExtensionAndCheck($filename){
        return pathinfo($filename,PATHINFO_EXTENSION);
    }

    public static function getFileName($etc){
        $date = date("Y-m-d H:i:s");
        $str = Commont::randStr(6);
        return $date . "-" . $str . "." . $etc;
    }
}