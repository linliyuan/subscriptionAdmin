<?php


namespace Models;

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    protected $table = '';
    protected $softDeleted = 1;

    public function initialize(){
        $this->setSource($this->table);
        if ($this->softDeleted == 1){
            $this->addBehavior(
                new Model\Behavior\SoftDelete([
                    'field' => 'deleted_at',
                    'value' => time(),
                ])
            );
        }
    }

    public function beforeCreate(){
        $this->created_at = date("Y-m-d H:i:s");
    }

    public function beforeUpdate(){
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function beforeValidationOnCreate(){
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function beforeValidationOnUpdate(){
        $this->updated_at = date("Y-m-d H:i:s");
    }
}