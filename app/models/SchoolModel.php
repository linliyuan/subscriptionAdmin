<?php

class SchoolModel extends \Models\BaseModel
{
    use \Traits\Model\SoftDelete;

    protected $table = 's_school';
//    protected $softDeleted = 1;

    public function initialize()
    {
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }
}