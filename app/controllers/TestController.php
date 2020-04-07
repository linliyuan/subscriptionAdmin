<?php
namespace Controllers;

use Models\UserModel;
use Phalcon\Di;
use Respect\Validation\Validator as V;
use \MongoDB\Client;

class TestController extends BaseController
{
    public function index(){
        V::key('test',V::numeric())
            ->check($this->param);


    }
}

