<?php


namespace Controllers;


use Models\UserModel;
use Phalcon\Di;
use Services\SubscribeService;
use Services\UserService;
use Utils\ErrorCode;
use Utils\RedisKey;
use Respect\Validation\Validator as v;

class UserController extends BaseController {
    public function login() {
        $code = $this->param['code'] ?? "";
        if (empty($code)) {
            return $this->responseFail(ErrorCode::$wrongParam, "参数错误");
        }

        $userInfo   = UserService::CodeToUserInfo($this->param['code'], $this->appId);
        $expireTime = time() + 3600;// 2天后过期
        /**
         * @var \Redis $redis
         */
        $redis              = Di::getDefault()->get('redis');
        $sessionKeyRedisKey = RedisKey::S_USER_SESSION_KEY . $userInfo['openid'];
        $redis->set($sessionKeyRedisKey, $userInfo['session_key'], 3600);

        $data = [
            'openid'      => $userInfo['openid'],
            'expire_time' => $expireTime,
        ];
        return $this->responseSuccess($data);
    }

    public function getUserStatus() {
        v::key('openid')
            ->check($this->param);
        $userStatus = UserService::getUserStatus($this->param["openid"]);

        return $this->responseSuccess($userStatus);
    }

    public function setUserInfo() {
        v::key("openid", v::stringType())
            ->key("iv", v::stringType())
            ->key("encryptedData", v::stringType())
            ->check($this->param);

        UserService::setUserInfo($this->appId, $this->param['openid'], $this->param['iv'], $this->param['encryptedData']);
        return $this->responseSuccess([]);
    }

    public function getUserInfo() {
        v::key("openid", v::stringType())
            ->check($this->param);

        $user     = UserService::getUserInfo($this->param['openid']);
        $userInfo = [
            "nickName"        => $user['nickName'],
            "schoolName"      => $user['schoolName'],
            "subscribeStatus" => (int)$user['subscribeStatus'],
            "isComplete" => (int)$user['isComplete'],
        ];
        return $this->responseSuccess($userInfo);
    }

    public function getUserCompleteInfo() {
        v::key("openid", v::stringType())
            ->check($this->param);
        $user = UserService::getUserCompleteInfo($this->param["openid"]);
        return $this->responseSuccess($user);
    }

    public function completeUserInfo() {
        v::key("openid", v::stringType())
            ->key("realName", v::stringType())
            ->key("miniPhone", v::stringType())
            ->key("gender", v::numeric())
            ->key("schoolId", v::numeric())
            ->key("departmentId", v::numeric())
            ->key("majorId", v::numeric())
            ->key("classId", v::numeric())
            ->key("identity", v::numeric())
            ->key("birthday", v::stringType())
            ->check($this->param);
        UserService::completeUserInfo($this->param);
        return $this->responseSuccess([]);
    }

    public function changeSubscribeStatus(){
        v::key("openid", v::stringType())
            ->key("subscribeStatus", v::in([0,1]))
            ->check($this->param);
        UserService::changeSubscribeStatus($this->param["openid"], $this->param["subscribeStatus"]);
        return $this->responseSuccess([]);
    }

    public function teacherList(){
        v::key("openid", v::stringType())
            ->check($this->param);
        return $this->responseSuccess(UserService::getTeacherList($this->param["openid"]));
    }
}