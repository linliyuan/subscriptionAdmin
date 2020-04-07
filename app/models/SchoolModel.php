<?php

class SchoolModel extends \Models\BaseModel
{
    use \Traits\Model\SoftDelete;

    protected $table = 's_user';
    protected $softDeleted = 1;

    public function initialize()
    {
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }
}