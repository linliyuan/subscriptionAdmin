<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

//只做路径配置，不做数据库等配置信息，将那些信息放在config,ini中配置
$appConfig = new \Phalcon\Config([
    'application'  => [
        'env'            => 'local',
        'debug'          => true,
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/lib/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'ToolDir'        => BASE_PATH . '/Tool/',
        'utilsDir'       => BASE_PATH . '/utils/',
        'logsDir'        => BASE_PATH . '/public/logs/',
        'sqlsDir'        => APP_PATH . '/sql_logs/',
        'ExceptionDir'   => APP_PATH . '/exceptions',
        'LibDir'         => APP_PATH . '/lib',
        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ],
    'database'     => [
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'dbname'   => 'subscription',
        'charset'  => 'utf8mb4',
        'port'     => 3306,
        'is_log'   => true // 是否登記sql日志
    ],
    'redis'        => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'db'   => 0,
        'auth' => ''
    ],
    'mongo'        => [
        'host'        => 'localhost:27017',
        'user'        => 'o_album',
        'password'    => 'o_album',
        'auth_source' => 'o_album',
        'database'    => 'subscription'
    ],
    'tokenservice' => [
        'host'            => '127.0.0.1:7666',
        'component_appid' => 'wx9ad710182d38d107'
    ],
    'snowflake'    => [
        'host' => '119.23.220.16:9091'
    ],
    'app'          => [
        'secret' => 'e5fb9880c95da3ffd9f7d6d85c1fbcb2'
    ]
]);

////单独创建一个config.ini，并从中引入数据库等配置信息(直接用config.php)
//$config = new \Phalcon\Config\Adapter\Ini(APP_PATH . '/config/config.ini');
//foreach ($config as $section => $obj){
//    foreach ($obj as $key =>$val){
//        if (strstr($val,'|')){
//            $config->$section->$key = explode('|' , $val);
//        }
//    }
//}
//$config->merge($appConfig);

return $appConfig;



