<?php


namespace Services;

use EasyWeChat\Factory;
use Exceptions\SessionKeyOverException;
use Exceptions\UserNotFoundException;
use Models\UserModel;
use Models\UserRelationModel;
use Phalcon\Di;
use Utils\Commont;
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

    // 获取用户的完整信息
    public static function getUserCompleteInfo($openid) {
        $userInfoSql = "SELECT
                            s_user.nickName,s_user.avatarUrl,s_user.miniPhone,s_user.gender,s_user.city,s_user.province,
                            s_user.country,s_user.isComplete,s_user.realName,s_user.birthday,s_user.schoolId,s_user.departmentId,
                            s_user.majorId,s_user.classId,s_user.identity,s_user.gender,
                            case s_user.identity when 1 then '学生' when 2 then '老师' when 3 then '普通职工' else '其他' end as identityValue,
                            case s_user.gender when 1 then '男' when 2 then '女' else '其他' end as genderValue,
                            s_school.`name` AS schoolName,
                            s_department.`name` AS departmentName,
                            s_major.`name` AS majorName,
                            s_class.`name` AS className
                        FROM
                            s_user
                            LEFT JOIN s_school ON s_user.schoolId = s_school.id 
                            LEFT JOIN s_department ON s_user.departmentId = s_department.id 
                            LEFT JOIN s_major ON s_user.majorId = s_major.id 
                            LEFT JOIN s_class ON s_user.classId = s_class.id 
                        WHERE
                            s_user.openid = \"{$openid}\"
                        Limit 1   
                        ";
        $res = Di::getDefault()->getShared('db')->query($userInfoSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $userInfo = $res->fetchall()[0] ?? [];
        if (empty($userInfo)) {
            throw new UserNotFoundException("用户找不到{$openid}");
        }
        foreach ($userInfo as &$value){
            if ($value == null){
                $value = "";
            }
        }
        $userInfo["birthday"] = $userInfo["birthday"] == "0000-00-00" ? "" : $userInfo["birthday"];
        return $userInfo;
    }
    public static function completeUserInfo($userCompletedInfo){
        $user = UserModel::getUserByOpenid($userCompletedInfo["openid"]);
        $user->realName = $userCompletedInfo["realName"];
        $user->miniPhone = $userCompletedInfo["miniPhone"];
        $user->gender = $userCompletedInfo["gender"];
        $user->birthday = $userCompletedInfo["birthday"];
        if ($user->isComplete != 1){
            $user->schoolId = $userCompletedInfo["schoolId"];
            $user->departmentId = $userCompletedInfo["departmentId"];
            $user->majorId = $userCompletedInfo["majorId"];
            $user->classId = $userCompletedInfo["classId"];
            $user->identity = $userCompletedInfo["identity"];
            $user->isComplete = 1;
        }
        $user->save();
    }
    public static function changeSubscribeStatus($openid, $subscribeStatus){
        $user = UserModel::getUserByOpenid($openid);
        $user->subscribeStatus = $subscribeStatus;
        $user->save();
    }

    public static function userSubscribeOther($openid, $otherOpenid){
        $relation = UserRelationModel::query()
            ->where("openid", "=", $openid)
            ->where("otherOpenid", "=", $otherOpenid)
            ->limit(1);
        $relation = json_decode(json_encode($relation), 1);
        if ($relation) {
            return;
        }
        $relation = new UserRelationModel();
        $relation->openid = $openid;
        $relation->otherOpenid = $otherOpenid;
        $relation->save();
    }

    public static function getTeacherList($openid){
        $user = UserModel::getUserByOpenid($openid);
        $school = $user->schoolId;
        $userInfoSql = "SELECT
                            s_user.realName as realName, s_department.name as departmentName, s_user.id as userId
                        FROM
                            s_user
                            LEFT JOIN s_department ON s_user.departmentId = s_department.id 
                        WHERE
                            s_user.schoolId = \"{$school}\"
                        AND s_user.identity = 2
                        ";
        $res = Di::getDefault()->getShared('db')->query($userInfoSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $teachers = $res->fetchall() ?? [];
        $teacherList = [];
        $teacherListShow = [];
//        var_dump($teachers);
//        exit();
        $teachers = Commont::ChineseSort($teachers, "realName");
        foreach ($teachers as $teacher){
            $teacherList[$teacher["departmentName"]][] = $teacher;
            $teacherListShow[$teacher["departmentName"]][] = $teacher["realName"];
        }
        return ["teacher_list" => $teacherList, "teacher_list_show" => $teacherListShow];
    }
}