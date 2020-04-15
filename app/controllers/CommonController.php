<?php


namespace Controllers;


use Respect\Validation\Validator as v;
use Services\CommonService;
use Services\UserService;
use Utils\ErrorCode;

class CommonController extends BaseController {
    public function uploadFile(){
//        v::key("openid", v::stringType())
//            ->check($this->param);
//        $openid = $this->param['openid'];
//        $status = UserService::getUserStatus($openid);
//        if ($status['is_auth'] != 1){
//            return $this->responseFail(ErrorCode::$userNotFound, "请先进行登陆");
//        }
        $res = CommonService::uploadFileService($_FILES['file']);
        if (!$res){
            return $this->responseFail(ErrorCode::$unknownError,CommonService::getError());
        }
        return $this->responseSuccess(["url" => $res]);
    }
}