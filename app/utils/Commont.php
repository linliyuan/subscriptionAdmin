<?php


namespace Utils;


class Commont {
    /**
     * 获取随机字符串
     * @param int $length 长度
     * @param string $type 类型
     * @param int $convert 转换大小写
     * @return string 随机字符串
     */
    public static function randStr($length = 6, $type = 'all', $convert = 0) {
        $config = array(
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'all'    => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );

        $string = $config[$type];

        $code   = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strlen)};
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }

    public static function ChineseSort($arr, $file) {
        if (empty($arr)){
            return $arr;
        }
        $sortFileArr = array_column($arr, $file);
        foreach ($sortFileArr as $key => $value) {
            $new_array[$key] = iconv('UTF-8', 'GBK', $value);
        }
        array_multisort($new_array, SORT_ASC, SORT_STRING, $arr);
        return $arr;
    }
}