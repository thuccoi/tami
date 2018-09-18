<?php

namespace Thuc\Zend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class OutsideController extends AbstractActionController {

    use \Thuc\Zend\ControllerTrait;

    private $client;
    private $user;

    public function __construct($dm, $ENV, $sessionContainer, $config) {

        $this->construct($dm, $ENV, $sessionContainer, $config);

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
        //user object
        $this->user = new \Thuc\Query\User($this->dm);
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

        $id = $this->code->param("id");


        $isactive = true;

        return new ViewModel([
            "id" => $id,
            "isactive" => $isactive
        ]);


        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function kichHoatAction() {

        $id = $this->code->param("id");

        $token = $this->code->get("token");

        if (!$id || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $id,
            "token" => $token
        ];

        return new ViewModel([
            "id" => $id,
            "activate" => TRUE,
            "message" => "Kích hoạt thành công"
        ]);



        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function datMatKhauAction() {

        $id = $this->code->param("id");

        $token = $this->code->get("token");

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

        $id = $this->code->param("id");

        $token = $this->code->get("token");
        $type = $this->code->get("type");

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
        if (!\Thuc\API\Client::verifyRespone($this->ENV, $this->config["google"]["capcha_secret"])) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->code->post("email");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $token = \Thuc\API\Client::generateToken($this->config["google"]["client_id"], $this->config["google"]["client_secret"]);

            if (!$token) {
                $this->code->error("Đã có lỗi xãy ra với máy chủ");
            }

            //send email verify
            $subject = "Khôi phục mật khẩu mới";
            $body = APP_URL . "/a/dat-mat-khau/{$email}?token={$token}";

            $verify = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
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

            $result = \Thuc\API\Client::login($email, $password, $this->config["google"]["client_id"], $this->config["google"]["client_secret"]);

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

        if (!\Thuc\API\Client::verifyRespone($this->ENV, $this->config["google"]['capcha_secret'])) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $first_name = $this->code->post("first_name");
        $last_name = $this->code->post("last_name");
        $phone = $this->code->post("phone");
        $email = $this->code->post("email");
        $password = $this->code->post("password");
        $repassword = $this->code->post("repassword");

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password && $password == $repassword) {

            $data = [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone" => $phone,
                "email" => $email,
                "password" => $password,
            ];

            $token = \Thuc\API\Client::generateToken($this->config["google"]['client_id'], $this->config["google"]['client_secret']);

            if (!$token) {
                $this->code->releaseError("Lỗi không tạo được khóa");
            }

            $data["token"] = $token;

            $query = $this->user->create($data);

            if ($query->status == 200) {

                //send email verify
                $subject = "Kích hoạt tài khoản";
                $body = APP_URL . "/a/kich-hoat/{$email}?token={$token}";

                $verify = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
                $verify->send();

                $this->code->success($query->message);
            } else {
                $this->code->error($query->message);
            }
        }

        $this->code->error("Bạn hãy điền đầy đủ thông tin, để thực hiện tạo tài khoản cho bạn.");
    }

    public function resetPasswordAction() {

        if (!\Thuc\API\Client::verifyRespone($this->ENV, $this->config["google"]['capcha_secret'])) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->code->param("id");

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

            $verify = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
            $verify->send();

            $this->code->success("Thay đổi mật khẩu thành công");
        }

        if ($password != $repassword) {
            $this->code->error("Mật khẩu và mật khẩu xác nhận không giống nhau.");
        }

        $this->code->error("Bạn hãy điền đầy đủ thông tin, để thực hiện đổi mật khẩu cho bạn.");
    }

}
