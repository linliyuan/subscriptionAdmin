<?php


namespace Services;

use EasyWeChat\Factory;
use Exceptions\SessionKeyOverException;
use Models\UserModel;
use Phalcon\Di;
use Utils\RedisKey;
use Utils\easyWechat;

class UserService
{
    /**
     * 通过 Code 获取用户信息
     * @param $code
     * @param $appId
     * @return mixed
     * @throws \Exception
     */
    public static function CodeToUserInfo($code,$appId){
        $config = easyWechat::getEasyWechatMinConfig($appId);
        $app = Factory::miniProgram($config);
        $userInfo = $app->auth->session($code);
        if (empty($userInfo)){
            throw new \Exception("获取用户信息失败：userInfo 为空");
        }
        if (empty($userInfo['openid']) || empty($userInfo['session_key'])){
            throw new \Exception("获取用户信息失败: " . ($userInfo['errcode'] ?? -1) . " " . ($userInfo['errmsg'] ?? ""));
        }

        return $userInfo;
    }

    public static function getUserStatus($openid) {
        $status = [
            "is_auth" => 0
        ];
        if (empty($openid)){
            return $status;
        }
        $user = UserModel::getUserByOpenid($openid);
        if ($user){
            $status["is_auth"] = 1;
        }
        return $status;
    }

    public static function setUserInfo($appId, $openid, $iv, $encryptedData) {
        // 检测sessionKey
        $sessionKey = UserService::getSessionKey($openid);
        $config = easyWechat::getEasyWechatMinConfig($appId);

        $app = Factory::miniProgram($config);
        $decryptedData = $app->encryptor->decryptData($sessionKey, $iv, $encryptedData);

        $user = UserModel::findFirst([
            "conditions" => "openid = :openid:",
            "bind" => [
                "openid" => $openid
            ]
        ]);
        if (!$user){
            $user = new UserModel();
        }
        $user->openid = $openid;
        $user->nickName = $decryptedData['nickName'];
        $user->avatarUrl = $decryptedData['avatarUrl'];
        $user->gender = $decryptedData['gender'];
        $user->city = $decryptedData['city'];
        $user->province = $decryptedData['province'];
        $user->country = $decryptedData['country'];

        $user->save();
    }

    public static function getSessionKey($openid) {
        /**
         * @var \Redis $redis
         */
        $redis = Di::getDefault()->get('redis');
        $sessionKey = $redis->get(RedisKey::S_USER_SESSION_KEY . $openid);
        if (!$sessionKey){
            throw new SessionKeyOverException();
        }
        return $sessionKey;
    }

    public static function getUserInfo($openid){
        $userInfoSql = "SELECT
                            s_user.*,
                            s_school.`name` AS schoolName 
                        FROM
                            s_user
                            LEFT JOIN s_school ON s_user.schoolId = s_school.id 
                        WHERE
                            s_user.openid = \"{$openid}\"
                        ";
        $res = Di::getDefault()->getShared('db')->query($userInfoSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $userInfo = $res->fetchall()[0];
        return $userInfo;
    }

}