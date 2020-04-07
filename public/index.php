<?php

use Exceptions\Handler;
use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
ini_set('date.timezone','Asia/Shanghai');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';
    include APP_PATH . '/Tool/Functions.php';

    /**
     * Include composer autoloader
     */
    require BASE_PATH . '/vendor/autoload.php';


    /**
     * Handle the request
     */
//    $application = new \Phalcon\Mvc\Application($di);

//    echo $application->handle()->getContent();
    $app = new \Phalcon\Mvc\Micro($di);

    include APP_PATH . '/app.php';

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}

try{
    set_error_handler("error_handler");
    set_exception_handler("exception_handler");

    $app->handle();
}catch (Throwable $e){

    $handler = new Exceptions\Handler();
    $response = $handler->handle($e);

    return $response->send();
}


