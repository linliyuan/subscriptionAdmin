<?php


namespace Controllers;


use Services\WorkService;

class WorkController extends BaseController {
    public function getWorkSetList() {
        $openid = $this->param['openid'] ?? "";

        $workSetList = WorkService::getWorkSetList($openid);
        return $this->responseSuccess(["workSetList" => $workSetList]);
    }
}