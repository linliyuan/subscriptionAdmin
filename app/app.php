<?php
//路由配置  单独写一个app.php设置路由路径

use Phalcon\Mvc\Micro\Collection as MicroCollection;

error_reporting(E_ALL);

$app->get('/',function (){
    echo "<h1> It works </h1>";
});

$test = new MicroCollection();
$test->setHandler(
    'Controllers\TestController',true
);
$test->setPrefix('/test');
$test->post('/index','index');
$app->mount($test);

$user = new MicroCollection();
$user->setHandler(
    'Controllers\UserController',true
);
$user->setPrefix('/user');
$user->post('/login','login');
$user->post('/get_status','getUserStatus');
$user->post('/set_user_info','setUserInfo');
$user->post('/get_user_info','getUserInfo');
$user->post('/set_msg','setMsg');
$app->mount($user);


$work = new MicroCollection();
$work->setHandler(
    'Controllers\WorkController',true
);
$work->setPrefix('/work');
$work->post('/get_work_set_list','getWorkSetList');
$app->mount($work);


/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(200, "Not Found")->sendHeaders();
    echo "<h1>404 Not found</h1>";
});

$app->before(function() use ($app)
{
    /**
     * @var \Phalcon\Mvc\Controller $oHandler
     */
    $handler_arr = $app->getActiveHandler();
    if (is_array($handler_arr) && !empty($handler_arr) && !empty($handler_arr[1]))
    {
        $defaults = [
            'controller' => preg_replace('/\\\(.*)\\\(.*)/i','$2',$handler_arr[0]->getDefinition()),
            'action' => $handler_arr[1],
        ];
        $app->router->setDefaults($defaults);
    }
});