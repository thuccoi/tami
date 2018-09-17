<?php

namespace Thuc\Zend;

Trait ControllerTrait {

    protected $dm;
    protected $code;
    protected $ENV;
    protected $sessionContainer;

    public function __construct($dm, $ENV, $sessionContainer) {
        $this->construct($dm, $ENV, $sessionContainer);
    }

    protected function construct($dm, $ENV, $sessionContainer) {
        $this->dm = $dm;
        $this->code = new \Thuc\Zend\Code($this);
        $this->ENV = $ENV;
        $this->sessionContainer = $sessionContainer;
    }

}
