<?php

if (!function_exists('request')){
    function request(){
        return \Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
    }
}

if (!function_exists("error_handler")){

    function error_handler($type,$message,$file,$line){
        $errorAsException = new \ErrorException($message,0,$type,$file,$line);
        throw $errorAsException;
    }
}

if (!function_exists("exception_handler")){

    function exception_handler(Throwable $exception){
        $handler = new Exceptions\Handler();
        $response = $handler->handle($exception);
        $response->send();

        exit();
    }
}

