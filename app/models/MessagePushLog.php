<?php


namespace Models;


class MessagePushLog extends \Models\BaseModel
{
    use \Traits\Model\SoftDelete;

    protected $table = 'message_push_log';
//    protected $softDeleted = 1;

    public function initialize()
    {
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }
}