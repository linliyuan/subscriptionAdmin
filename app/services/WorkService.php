<?php


namespace Services;


use Models\UserModel;
use Models\WorkSetModel;

class WorkService {
    // 默认的工作设置数组
    const DEFAULT_WORK_SETTING = [
        "icon"   => "photo-o",
        "page"   => "/pages/work/work",
        "text"   => "暂未开放",
        "limits" => "1"
    ];

    public static function getWorkSetList($openid) {
        $totalSetNum = 6; // 默认设置六个项，不够用默认的补全
        $user = UserModel::getUserByOpenid($openid);
        $limits = [];
        $workSetList = [];
        // 按不同的身份设置权限
        if (!empty($user->identity)){
            switch ($user->identity){
                case 1:
                    $limits[] = WorkSetModel::LIMIT_ONLY_STUDENT;
                    $limits[] = WorkSetModel::LIMIT_STUDENT_AND_STUDENT;
                    break;
                case 2:
                    $limits[] = WorkSetModel::LIMIT_ONLY_TEACHER;
                    $limits[] = WorkSetModel::LIMIT_STUDENT_AND_STUDENT;
                    break;
            }
        }
        if (empty($limits)){
            return $workSetList;
        }
        $workSetList = WorkSetModel::find([
            "conditions" => "limits in ({limits:array})",
            "bind" => [
                "limits" => $limits
            ]
        ]);
        $workSetList = $workSetList->toArray();
        if (count($workSetList) < $totalSetNum){
            $addNum = $totalSetNum -  count($workSetList);
            for ($i=0;$i<$addNum;$i++){
                $workSetList[] = self::DEFAULT_WORK_SETTING;
            }
        }
        return $workSetList;

    }
}