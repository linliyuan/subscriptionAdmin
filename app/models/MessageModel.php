<?php


namespace Models;


use Exceptions\MsgNotFoundException;

class MessageModel extends \Models\BaseModel
{
    use \Traits\Model\SoftDelete;

    protected $table = 's_school';
//    protected $softDeleted = 1;

    public function initialize()
    {
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }

    public static function getMsgById ($msgId){
        $msg = UserModel::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $msgId
            ]
        ]);
        if (!$msg) {
            throw new MsgNotFoundException("信息找不到{$msgId}");
        }
        return $msg;
    }
}