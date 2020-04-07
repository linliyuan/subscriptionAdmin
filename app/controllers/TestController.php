<?php
namespace Controllers;

use Respect\Validation\Validator as V;

class TestController extends BaseController
{
    public function index(){
        V::key('test',V::numeric())
            ->check($this->param);


    }
}

