<?php


namespace Models;


class WorkSetModel extends BaseModel {
    use \Traits\Model\SoftDelete;
    const LIMIT_ONLY_STUDENT = 1;
    const LIMIT_ONLY_TEACHER = 2;
    const LIMIT_STUDENT_AND_STUDENT = 3;
    protected $table = 's_work_set';
//    protected $softDeleted = 1;
    public function initialize(){
        $this->setSource($this->table);
//        $this->setSoftDelete();
    }
}