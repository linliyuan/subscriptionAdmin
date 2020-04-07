<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Events\Manager;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileLogger;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    $connection = new $class($params);

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }
    if (!empty($config->database->is_log)){
        //写入sql执行语句
        //设置日期格式
        $formatter = new \Phalcon\Logger\Formatter\Line();
        $formatter->setFormat('[%date%][%type%] %message%');
        $formatter->setDateFormat('Y-m-d H:i:s');

        $logger = new  \Phalcon\Logger\Adapter\File($config->application->sqlsDir . 'log-' .date("Y-m-d") . ".log");
        $logger->setFormatter($formatter);

        $eventsManager = new Phalcon\Events\Manager();

        // Listen all the database events
        $eventsManager->attach(
            'db:beforeQuery',
            function ($event, $connection) use ($logger) {
                $logger->info(
                    $connection->getSQLStatement()
                );
            }
        );
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});

//$di->setShared('mongo',function (){
//   $config = $this->getConfig();
//
//    $uri='mongodb://'.$config->mongo->host;
//    $client = new \MongoDB\Client($uri,[
//        'username'=>$config->mongo->user,
//        'password'=>$config->mongo->password,
//        'authSource'=>$config->mongo->auth_source,
//    ]);
//    $collection = $client->selectDatabase('laravel_phaclon');
//    return $collection;
//});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Redis connection is created based in the parameters defined in the configuration file
 */
$di->setShared('redis', function () {
    $config = $this->getConfig();
    $redis = new \Redis();
    $redis->connect($config->redis->host,$config->redis->port);
    if(isset($config->redis->auth)&&$config->redis->auth){
        $redis->auth($config->redis->auth);
    }
    $redis->select($config->redis->db);
    return $redis;
});



$di->setShared('logger',function (){
   $config = $this->getConfig();
   //设置日期格式
   $formatter = new \Phalcon\Logger\Formatter\Line();
   $formatter->setFormat('[%date%][%type%] %message%');
   $formatter->setDateFormat('Y-m-d H:i:s');

   $logger = new  \Phalcon\Logger\Adapter\File($config->application->logsDir . 'log-' .date("Y-m-d") . ".log");
    $logger->setFormatter($formatter);

    return $logger;
});
