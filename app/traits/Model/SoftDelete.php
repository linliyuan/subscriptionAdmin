<?php
namespace Traits\Model;

trait SoftDelete{
    private function setSoftDelete(){
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\SoftDelete([
                'field' => 'deleted_at',
                'value' => time()
            ])
        );
    }
}
