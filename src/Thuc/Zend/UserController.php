<?php

namespace Thuc\Zend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class UserController extends AbstractActionController {

    use \Thuc\Zend\ControllerTrait;

    private $user;

    public function __construct($dm, $ENV, $sessionContainer, $config) {
        $this->construct($dm, $ENV, $sessionContainer, $config);
        //user object
        $this->user = new \Thuc\Query\User($this->dm);
    }

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
        $firstname = $this->code->post("firstname");
        $lastname = $this->code->post("lastname");
        $title = $this->code->post("title");
        $phone = $this->code->post("phone");
        $address = $this->code->post("address");
        $info = $this->code->post("info", true);
        $public = $this->code->post("public");
        $password = $this->code->post("password");
        $password1 = $this->code->post("password1");

        $data = [
            "first_name" => $firstname,
            "last_name" => $lastname,
            "title" => $title,
            "phone" => $phone,
            "address" => $address,
            "info" => $info,
            "public" => $public
        ];

        if ($password || $password1) {
            if ($password === $password1) {
                $data["password"] = $password;
            } else {
                $this->code->error("Mật khẩu và mật khẩu xác nhận không giống nhau");
            }
        }

        //update information
        $update = $this->user->update($this->sessionContainer->viewer->getId(), $data);

        if ($update === true) {
            
            $this->sessionContainer->viewer = $this->user->getOne($this->sessionContainer->viewer->getId());
            
            $this->code->success("Cập nhật thông tin của bạn thành công");
        }

        $this->code->error("Thông tin của bạn chưa được thay đổi");
    }

}
