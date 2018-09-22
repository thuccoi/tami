<?php

namespace Thuc\Zend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class UserController extends AbstractActionController {

    use \Thuc\Zend\ControllerTrait;

    public function onDispatch(MvcEvent $e) {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);

        // Set alternative layout
        $this->layout()->setTemplate('system/layout');
        $this->layout()->ENV = $this->ENV;

        if (!$this->sessionContainer || !isset($this->sessionContainer->viewer)) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $this->layout()->viewer = $this->sessionContainer->viewer;

        // Return the response
        return $response;
    }

    public function indexAction() {

        return [
        ];
    }

    public function hoSoAction() {
        return [
            "viewer" => $this->sessionContainer->viewer
        ];
    }

    public function editAction() {
        return [];
    }

}
