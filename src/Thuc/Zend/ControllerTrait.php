<?php

namespace Thuc\Zend;

Trait ControllerTrait {

    protected $dm;
    protected $code;
    protected $ENV;
    protected $sessionContainer;
    protected $config;

    public function __construct($dm, $ENV, $sessionContainer, $config) {
        $this->construct($dm, $ENV, $sessionContainer, $config);
    }

    protected function construct($dm, $ENV, $sessionContainer, $config) {
        $this->dm = $dm;
        $this->code = new \Thuc\Zend\Code($this);
        $this->ENV = $ENV;
        $this->sessionContainer = $sessionContainer;
        $this->config = $config;
    }

}
