<?php
namespace Models;

use Exceptions\UserNotFoundException;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class UserModel extends BaseModel
{
    use \Traits\Model\SoftDelete;

    protected $table = 's_user';
    protected $softDeleted = 1;
    public function initialize(){
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }

    /**
     * @param $openid
     * @return \Phalcon\Mvc\Model
     * @throws UserNotFoundException
     */
    public static function getUserByOpenid($openid) {
        $user = UserModel::findFirst([
            "conditions" => "openid = :openid:",
            "bind" => [
                "openid" => $openid
            ]
        ]);
        if (!$user) {
            throw new UserNotFoundException("用户找不到{$openid}");
        }
        return $user;
    }
}