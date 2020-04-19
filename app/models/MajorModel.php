<?php


namespace Models;


class MajorModel extends BaseModel {
    use \Traits\Model\SoftDelete;

    protected $table       = 's_major';
    protected $softDeleted = 1;

    public function initialize() {
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }


}