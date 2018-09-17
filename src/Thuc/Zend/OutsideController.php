<?php

namespace Thuc\Zend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class OutsideController extends AbstractActionController {

    use \Thuc\Zend\ControllerTrait;

    private $client;

    public function __construct($dm, $ENV) {

        $this->construct($dm, $ENV);

        $this->client = new \Google_Client();
        $this->client->setAuthConfig(ROOT_DIR . '/public/client_secret.json');

        $this->client->addScope(\Google_Service_Drive::DRIVE_METADATA_READONLY);
        $this->client->addScope(\Google_Service_Oauth2::PLUS_LOGIN);
        $this->client->addScope(\Google_Service_Oauth2::PLUS_ME);
        $this->client->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);
        $this->client->addScope(\Google_Service_Oauth2::USERINFO_PROFILE);

        $this->client->setRedirectUri(APP_URL . '/a/verify');
        $this->client->setAccessType('offline');        // offline access
        $this->client->setIncludeGrantedScopes(true);   // incremental auth
    }

    //set layout
    function onDispatch(MvcEvent $e) {

        $response = parent::onDispatch($e);

        $this->layout()->setTemplate('outside/layout');

        return $response;
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function dangNhapAction() {
        return new ViewModel();
    }

    public function googleLoginAction() {
        $auth_url = $this->client->createAuthUrl();
        return $this->redirect()->toUrl(filter_var($auth_url, FILTER_SANITIZE_URL));
    }

    public function dangKyAction() {

        return new ViewModel();
    }

    public function quenMatKhauAction() {

        return new ViewModel();
    }

    public function xacNhanAction() {

        $id = $this->params("id");

        $viewer = \Application\Core\Client::getUser($id);

        if ($viewer) {
            $isactive = true;
            if ($viewer->status != \Application\Oauth\User::$isActive) {
                $isactive = false;
            }

            return new ViewModel([
                "id" => $id,
                "isactive" => $isactive
            ]);
        }

        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function kichHoatAction() {

        $id = $this->params("id");

        $token = $this->params()->fromQuery("token");

        if (!$id || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $id,
            "token" => $token
        ];

        $result = \Application\Model\Curl::callAPIM2(API_SYSTEM_URL . "/activate-user", $data, "POST", \Application\Core\Client::generateToken());

        if ($result && isset($result->status) && $result->status == 200) {
            return new ViewModel([
                "id" => $id,
                "activate" => TRUE,
                "message" => $result->detail
            ]);
        }

        if ($result) {
            return new ViewModel([
                "id" => $id,
                "activate" => FALSE,
                "message" => $result->detail
            ]);
        }


        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function datMatKhauAction() {

        $id = $this->params("id");

        $token = $this->params()->fromQuery("token");

        if (!$id || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $id,
            "token" => $token
        ];


        return new ViewModel([
            "email" => $id,
            "token" => $token
        ]);



        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function thayDoiMatKhauAction() {

        $id = $this->params("id");

        $token = $this->params()->fromQuery("token");
        $type = $this->params()->fromQuery("type");

        if (!$id || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $id,
            "token" => $token
        ];

        return new ViewModel([
            "email" => $id,
            "token" => $token,
            "type" => $type
        ]);



        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function lostPasswordAction() {
        if (!$this->code->verifyRespone()) {
            $this->code->releaseError("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->code->getEmail("email");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $token = \Application\Core\Client::generateToken();

            if (!$token) {
                $this->code->releaseError("Đã có lỗi xãy ra với máy chủ");
            }

            $sessionOauth = new Container('oauth');
            $sessionOauth->offsetSet("token", $token);

            $viewer = \Application\Core\Client::getUser($email);
            if ($viewer) {

                //send email verify
                $subject = "Khôi phục mật khẩu mới";
                $body = APP_URL . "/a/dat-mat-khau/{$email}?token={$viewer->token}";

                $verify = new \Application\Model\Mail($subject, $body, $email);
                @$verify->send();

                $this->code->releaseSuccess("Bạn hãy vào email để thực hiện khôi phục mật khẩu");
            } else {

                $this->code->releaseError("Đã có lỗi xãy ra");
            }
        }

        $this->code->releaseError("Bạn hãy điền email của mình để khôi phục mật khẩu.");
    }

    public function verifyAction() {
        $this->client->authenticate($this->code->get("code"));
        $access_token = $this->client->getAccessToken();
        if ($access_token && isset($access_token["access_token"])) {

            $oAuth = new \Google_Service_Oauth2($this->client);
            $userData = $oAuth->userinfo_v2_me->get();

            $email = $userData->getEmail();

            $sessionContainer = $this->getEvent()->getParam('sessionContainer', false);
            $sessionContainer->email = $email;

            echo "<pre>";
            print_r($userData);
            exit;
        }

        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function loginAction() {

        $email = $this->code->getEmail("email");
        $password = $this->code->getInline("password");

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {

            $result = \Application\Core\Client::login($email, $password);

            if ($result == true) {
                $this->code->releaseSuccess("Đăng nhập thành công");
            } else {
                $this->code->releaseError("Thông tin tài khoản không chính xác.");
            }
        }

        $this->code->releaseError("Bạn hãy điền đầy đủ thông tin, để thực hiện đăng nhập.");
    }

    public function registerAction() {

        if (!$this->code->verifyRespone()) {
            $this->code->releaseError("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $first_name = $this->code->getInline("first_name");
        $last_name = $this->code->getInline("last_name");
        $phone = $this->code->getInline("phone");
        $email = $this->code->getEmail("email");
        $password = $this->code->getInline("password");
        $repassword = $this->code->getInline("repassword");

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password && $password == $repassword) {

            $data = [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone" => $phone,
                "email" => $email,
                "password" => $password,
            ];

            $token = \Application\Core\Client::generateToken();

            if (!$token) {
                $this->code->releaseError("Lỗi không tạo được khóa");
            }

            $sessionOauth = new Container('oauth');
            $sessionOauth->offsetSet("token", $token);

            $result = \Application\Model\Restful::Post(API_SYSTEM_URL . "/users", $data);

            if ($result) {

                //send email verify
                $subject = "Kích hoạt tài khoản";
                $body = APP_URL . "/a/kich-hoat/{$email}?token={$result->token}";

                $verify = new \Application\Model\Mail($subject, $body, $email);
                @$verify->send();

                $this->code->releaseSuccess("Đăng ký thành công", $result);
            } else {

                $this->code->releaseError("Lỗi ở máy chủ");
            }
        }

        $this->code->releaseError("Bạn hãy điền đầy đủ thông tin, để thực hiện tạo tài khoản cho bạn.");
    }

    public function resetPasswordAction() {

        if (!$this->code->verifyRespone()) {
            $this->code->releaseError("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->params("id");

        $token = $this->code->getInline("token");

        $password = $this->code->getInline("password");
        $repassword = $this->code->getInline("repassword");

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password && $password == $repassword && $token) {

            $data = [
                "email" => $email,
                "password" => $password,
                "token" => $token,
            ];

            $result = \Application\Model\Curl::callAPIM2(API_SYSTEM_URL . "/reset-password", $data, "POST", \Application\Core\Client::generateToken());

            if ($result && isset($result->status) && $result->status == 200) {

                //send email verify
                $subject = "Thay đổi mật khẩu thành công";
                $body = APP_URL . "/a/thay-doi-mat-khau/{$email}?token={$result->data->token}&type=reset-password";

                $verify = new \Application\Model\Mail($subject, $body, $email);
                @$verify->send();

                $this->code->releaseSuccess("Thay đổi mật khẩu thành công", $result->data);
            } else {

                $this->code->releaseError($result->detail);
            }
        }

        if ($password != $repassword) {
            $this->code->releaseError("Mật khẩu và mật khẩu xác nhận không giống nhau.");
        }

        $this->code->releaseError("Bạn hãy điền đầy đủ thông tin, để thực hiện đổi mật khẩu cho bạn.");
    }

}
