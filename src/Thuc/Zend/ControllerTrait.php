<?php

namespace Thuc\Zend;

use Zend\Mvc\MvcEvent;

Trait ControllerTrait {

    protected $dm;
    protected $code;
    protected $ENV;
    protected $sessionManager;

    public function __construct($dm, $ENV) {
        $this->construct($dm, $ENV);
    }

    protected function construct($dm, $ENV) {
        $this->dm = $dm;
        $this->code = new \Thuc\Zend\Code($this);
        $this->ENV = $ENV;
    }

    protected function dispatch(MvcEvent $e) {
        $this->sessionManager = $this->getEvent()->getParam('sessionContainer', false);
    }

}
