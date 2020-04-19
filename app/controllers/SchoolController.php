<?php


namespace Controllers;


use Services\SchoolService;
use Utils\ErrorCode;

class SchoolController extends BaseController {
    function GetSchoolList() {
        $schoolList = SchoolService::getTotalSchoolList();

        return $this->responseSuccess(["school_list" => $schoolList]);
    }

    function GetDepartmentList(){
        $schoolId = $this->param["schoolId"] ?? 0;
        if ($schoolId == 0){
            return $this->responseFail(ErrorCode::$wrongParam, "schoolId is required");
        }

        $departmentList = SchoolService::getDepartmentList($schoolId);
        return $this->responseSuccess(["departmentList" => $departmentList]);
    }

    function GetMajorList(){
        $departmentId = $this->param["departmentId"] ?? 0;
        if ($departmentId == 0){
            return $this->responseFail(ErrorCode::$wrongParam, "schoolId is required");
        }

        $majorList = SchoolService::getMajorList($departmentId);
        return $this->responseSuccess(["majorList" => $majorList]);
    }

    function GetClassList(){
        $majorId = $this->param["majorId"] ?? 0;
        if ($majorId == 0){
            return $this->responseFail(ErrorCode::$wrongParam, "schoolId is required");
        }

        $classList = SchoolService::getClassList($majorId);
        return $this->responseSuccess(["classList" => $classList]);
    }
}