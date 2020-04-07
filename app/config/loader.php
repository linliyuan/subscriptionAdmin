<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        //        $config->application->tasksDir,
        //        $config->application->traitsDir,
        $config->application->utilsDir,
        $config->application->ExceptionDir,
        $config->application->LibDir,
        //        $config->application->protoDir,
        //        $config->application->ModuleDir,
    ]
)->register();

$loader->registerNamespaces([
    'Controllers' => APP_PATH . '/controllers/',
    'Models'      => APP_PATH . '/models/',
    'Services'    => APP_PATH . '/services/',
    'Traits'      => APP_PATH . '/traits/',
    'Logs'        => APP_PATH . '/logs/',
    'Caches'      => APP_PATH . '/caches/',
    'Utils'       => APP_PATH . '/utils/',
    'Exceptions'  => APP_PATH . '/exceptions/',
    'Proto'       => APP_PATH . '/proto/',
    'Module'      => APP_PATH . '/module/',
    'Lib'         => APP_PATH . '/lib/',
]);
