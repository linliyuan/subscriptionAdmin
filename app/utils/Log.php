<?php
namespace Utils;

class Log
{
    private static $logger;

    public static function getLogger(){
        if (self::$logger == null){
            self::$logger = \Phalcon\Di::getDefault()['logger'];
        }

        return self::$logger;
    }

    /**
     * @param $message
     * @param array|null $context
     * @return mixed
     */

    public static function error($message,array $context = null){
        $logger = self::getLogger();

        return $logger->error($message . "",$context);
    }

    /**
     * @param $message
     * @param array|null $context
     * @return mixed
     */
    public static function info($message,array $context = null){
        $logger = self::getLogger();

        return $logger->info($message . "",$context);
    }
}