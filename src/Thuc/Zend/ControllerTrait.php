<?php

namespace Thuc\Zend;

Trait ControllerTrait {

    protected $dm;
    protected $code;
    protected $ENV;

    public function __construct($dm, $ENV) {
        $this->construct($dm, $ENV);
    }

    protected function construct($dm, $ENV){
        $this->dm = $dm;
        $this->code = new \Thuc\Zend\Code($this);
        $this->ENV = $ENV;
    }
}
