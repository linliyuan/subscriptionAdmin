<?php
namespace Controllers;

use GuzzleHttp\Client;
use Utils\ErrorCode;

class BaseController extends \Phalcon\Mvc\Controller
{
    //protected $verifyMode;
    protected $controller;
    protected $action;
    protected $cliVersion;
    protected $appId;
    protected $param = [];

    public function onConstruct(){
        //get current router info
        $default = $this->router->getDefaults();
        $this->controller = substr($default['controller'],0,strpos($default['controller'],'Controller'));
        $this->action = $default['action'];
        //decode referer in header
        $referer = $this->request->getHTTPReferer() ?? null;
        if ($referer) {
            preg_match("/^https:\/\/servicewechat\.com\/([^\/]*)\/([^\/]*)\/(page-frame\.html)?/", $referer, $refer_args);
            if (count($refer_args)) {
                $this->appId = $refer_args[1];
                $this->cliVersion = $refer_args[2];
            }
        }
        if(empty($this->appId )){
            //h5情况下直接在body下传输，写入到this->appid做兼容
            $this->appId = $this->request->get('appid') ?? "";
        }
        //params in url
        $getData = $this->request->get();
        foreach ($getData as $key => $val) {
            if ('_' == $key[0]) {
                unset($getData[$key]);
            }
        }
        //params in form-data or x-www-form-urlencoded
        $postData = $this->request->getPost();
        $this->param = array_merge($this->param, $getData, $postData);

        if($this->config->application->env == 'prod'){
            $this->client = new Client();
            if(empty($this->appId)){
                return $this->responseFail(ErrorCode::$wrongParam,"missing appid.");
            }
            if(!isset($this->param['client_version'])){
                return $this->responseFail(ErrorCode::$wrongParam,"missing client_version.");
            }
        }else{
            $this->client = new Client(['verify' => false]);
        }
    }

    protected function responseSuccess($data)
    {
        $res = [
            'errCode' => 0,
            'errMsg' => 'ok.',
            'data' => $data,
        ];
        $this->responseJson($res);
        exit();
    }

    protected function responseFail($errCode = null, $errMsg = 'an error occurred.',$isShow = 0)
    {

        $res = [
            'errCode' => $errCode,
            'errMsg' => $errMsg,
            'isShow' => $isShow
        ];
        $this->responseJson($res);
        exit();
    }

    private function responseJson($data)
    {
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Content-Type", "application/json");

        if (!is_string($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        $response->setContent($data);

        $response->send();
    }

}