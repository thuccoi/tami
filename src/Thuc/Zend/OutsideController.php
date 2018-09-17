<?php

namespace Thuc\Zend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use System\Query\Client;
use System\Query\Mail;

class OutsideController extends AbstractActionController {

    use \Thuc\Zend\ControllerTrait;

    private $client;

    public function __construct($dm, $ENV, $sessionContainer) {

        $this->construct($dm, $ENV, $sessionContainer);

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

        if ($this->sessionContainer && isset($this->sessionContainer->email)) {
            return $this->redirect()->toRoute("langguage");
        }

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

        $viewer = Client::getUser($id);

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
        if (!Client::verifyRespone()) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->code->post("email");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $token = Client::generateToken();

            if (!$token) {
                $this->code->error("Đã có lỗi xãy ra với máy chủ");
            }

            //send email verify
            $subject = "Khôi phục mật khẩu mới";
            $body = APP_URL . "/a/dat-mat-khau/{$email}?token={$token}";

            $verify = new Mail($subject, $body, $email);
            $verify->send();

            $this->code->success("Bạn hãy vào email để thực hiện khôi phục mật khẩu");
        }

        $this->code->error("Bạn hãy điền email của mình để khôi phục mật khẩu.");
    }

    public function verifyAction() {
        $this->client->authenticate($this->code->get("code"));
        $access_token = $this->client->getAccessToken();
        if ($access_token && isset($access_token["access_token"])) {

            $oAuth = new \Google_Service_Oauth2($this->client);
            $userData = $oAuth->userinfo_v2_me->get();

            $email = $userData->getEmail();

            $this->sessionContainer->email = $email;
        }

        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function loginAction() {

        $email = $this->code->post("email");
        $password = $this->code->post("password");

        $incorrect = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password) {

            $result = Client::login($email, $password);

            if ($result === true) {
                $this->sessionContainer->email = $email;
            } else {
                $incorrect = true;
            }
        } else {
            $incorrect = true;
        }

        if ($incorrect === true) {
            echo "Thông tin đăng nhập không đúng";
            exit;
        }


        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function registerAction() {

        if (!Client::verifyRespone()) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
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

            $token = Client::generateToken();

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

                $this->code->success("Đăng ký thành công", $result);
            } else {

                $this->code->error("Lỗi ở máy chủ");
            }
        }

        $this->code->error("Bạn hãy điền đầy đủ thông tin, để thực hiện tạo tài khoản cho bạn.");
    }

    public function resetPasswordAction() {

        if (!Client::verifyRespone()) {
            $this->code->releaseError("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->params("id");

        $token = $this->code->post("token");

        $password = $this->code->post("password");
        $repassword = $this->code->post("repassword");

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password && $password == $repassword && $token) {

            $data = [
                "email" => $email,
                "password" => $password,
                "token" => $token,
            ];


            //send email verify
            $subject = "Thay đổi mật khẩu thành công";
            $body = APP_URL . "/a/thay-doi-mat-khau/{$email}?token={$token}&type=reset-password";

            $verify = new \Application\Model\Mail($subject, $body, $email);
            $verify->send();

            $this->code->success("Thay đổi mật khẩu thành công");
        }

        if ($password != $repassword) {
            $this->code->error("Mật khẩu và mật khẩu xác nhận không giống nhau.");
        }

        $this->code->error("Bạn hãy điền đầy đủ thông tin, để thực hiện đổi mật khẩu cho bạn.");
    }

}
