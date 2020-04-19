<?php


namespace Services;


use Phalcon\Di;
use Utils\Commont;

class SchoolService {
    public static function getTotalSchoolList() {
        $schools = \SchoolModel::find()->toArray();
        $schoolList = [];
        $schoolListShow = [];
        $schools = Commont::ChineseSort($schools, "name");
        foreach ($schools as $school){
            $schoolList[$school["province"]][] = $school;
            $schoolListShow[$school["province"]][] = $school["name"];
        }
        return ["school_list" => $schoolList, "school_list_show" => $schoolListShow];
    }

    public static function getDepartmentList($schoolId) {
        $departmentListSql = "SELECT
                            s_department.*
                        FROM
                            s_school_department_relation
                            LEFT JOIN s_department ON s_school_department_relation.departmentId = s_department.id 
                        WHERE
                            s_school_department_relation.schoolId = \"{$schoolId}\"
                        ";
        $res = Di::getDefault()->getShared('db')->query($departmentListSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $departmentList = $res->fetchall();
        $departmentList = Commont::ChineseSort($departmentList, "name");
        return $departmentList;
    }

    public static function getMajorList($departmentId) {
        $majorListSql = "SELECT
                            s_major.*
                        FROM
                            s_department_major_relation
                            LEFT JOIN s_major ON s_department_major_relation.majorId = s_major.id 
                        WHERE
                            s_department_major_relation.departmentId = \"{$departmentId}\"
                        ";
        $res = Di::getDefault()->getShared('db')->query($majorListSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $majorList = $res->fetchall();
        $majorList = Commont::ChineseSort($majorList, "name");
        return $majorList;
    }

    public static function getClassList($majorId) {
        $classListSql = "SELECT
                            *
                        FROM
                            s_class
                        WHERE
                            s_class.majorId = \"{$majorId}\"
                        ";
        $res = Di::getDefault()->getShared('db')->query($classListSql);
        $res->setFetchMode(
            \Phalcon\Db::FETCH_ASSOC
        );
        $classList = $res->fetchall();
        $classList = Commont::ChineseSort($classList, "name");
        return $classList;
    }
}