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

        //enviroment
        $this->layout()->ENV = $this->ENV;

        $this->layout()->setTemplate('outside/layout');


        if ($this->sessionContainer && isset($this->sessionContainer->viewer)) {
            return $this->redirect()->toRoute("admin");
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

        $email = $this->code->param("id");

        $token = $this->code->get("token");

        if (!$email || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $email,
            "token" => $token
        ];

        return new ViewModel([
            "email" => $email,
            "respone" => $this->user->activate($data)
        ]);
    }

    public function datMatKhauAction() {

        $email = $this->code->param("id");

        $token = $this->code->get("token");

        if (!$email || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $email,
            "token" => $token
        ];

        $check = $this->user->checkToken($data);
        if ($check->status == 200) {
            return new ViewModel($data);
        }


        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function thayDoiMatKhauAction() {

        $email = $this->code->param("id");

        $token = $this->code->get("token");
        $type = $this->code->get("type");

        if (!$email || !$token) {
            return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
        }

        $data = [
            "email" => $email,
            "token" => $token,
            "type" => $type
        ];

        $check = $this->user->checkToken($data);
        if ($check->status == 200) {
            return new ViewModel($data);
        }

        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function lostPasswordAction() {
        if (!\Thuc\API\Client::verifyRespone($this->ENV, $this->config["google"]["capcha_secret"])) {
            $this->code->error("Bạn hãy xác nhận mình không phải là robot, tự động tạo tài khoản trước.");
        }

        $email = $this->code->post("email");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $user = $this->user->getOne($email);
            if (!$user) {
                $this->code->error("Không tìm thấy tài khoản có địa chỉ email này không có trong hệ thống");
            }

            $token = \Thuc\API\Client::generateToken($this->config["google"]["client_id"], $this->config["google"]["client_secret"]);

            if (!$token) {
                $this->code->error("Đã có lỗi xãy ra với máy chủ");
            }

            $user->setToken($token);
            $this->dm->persist($user);
            $this->dm->flush();
            $this->dm->clear();

            //send email verify
            $subject = "Khôi phục mật khẩu mới";
            $body = APP_URL . "/a/dat-mat-khau/{$email}?token={$user->getToken()}";

            $verify = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
            $verify->send();

            $this->code->success("Chúng tôi đã gửi một đường dẫn tới địa chỉ email {$email}.");
        }

        $this->code->error("Bạn hãy điền email của mình để khôi phục mật khẩu.");
    }

    public function errorAction() {
        return [];
    }

    public function logOutAction() {
        //remove viewer
        unset($this->sessionContainer->viewer);
        
        return $this->redirect()->toRoute("outside", ["action" => "dang-nhap"]);
    }

    public function verifyAction() {
        $this->client->authenticate($this->code->get("code"));
        $access_token = $this->client->getAccessToken();
        if ($access_token && isset($access_token["access_token"])) {

            $oAuth = new \Google_Service_Oauth2($this->client);
            $userData = $oAuth->userinfo_v2_me->get();

            $email = $userData->getEmail();

            //get email in system
            $user = $this->user->getOne($email);

            if (!$user) {
                $token = \Thuc\API\Client::generateToken($this->config["google"]['client_id'], $this->config["google"]['client_secret']);
                if (!$token) {
                    return $this->redirect()->toRoute("outside", ["action" => "error"]);
                }

                $data = [
                    "first_name" => $userData->getGivenName(),
                    "last_name" => $userData->getFamilyName(),
                    "email" => $email,
                    "picture" => $userData->getPicture(),
                    "password" => $token,
                    "token" => $token,
                    "create_from" => \Thuc\Oauth\User::$FROM_GOOGLE,
                    "client_id" => $this->config["google"]['client_id']
                ];

                $query = $this->user->create($data);

                if ($query->status == 200) {
                    //send email verify
                    $subject = "Truy cập mới tại " . APP_URL;
                    $body = "Chúng tôi vừa tạo cho bạn một tài khoản mới tại " . APP_URL . " để có thể truy cập trực tiếp vào hệ thống qua tài khoản mời bạn nhấp vào đường dẫn " . APP_URL . "/a/dat-mat-khau/{$email}?token={$token}  để thực hiện đặt mật khẩu cho mình.";

                    $send = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
                    $send->send();
                } else {
                    return $this->redirect()->toRoute("outside", ["action" => "error"]);
                }

                //get user
                $user = $this->user->getOne($email);
            }

            $this->sessionContainer->viewer = $user;
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
                $user = $this->user->getOne($email);

                if (!$user) {
                    $incorrect = true;
                } else if (!$user->isActive()) {
                    $this->code->error("Tài khoản này chưa được kích hoạt");
                } else {
                    $this->sessionContainer->viewer = $user;
                }
            } else {
                $incorrect = true;
            }
        } else {
            $incorrect = true;
        }

        if ($incorrect === true) {
            $this->code->error("Thông tin đăng nhập không đúng");
        }

        $this->code->success("Đăng nhập thành công");
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
                "client_id" => $this->config["google"]['client_id']
            ];

            $token = \Thuc\API\Client::generateToken($this->config["google"]['client_id'], $this->config["google"]['client_secret']);

            if (!$token) {
                $this->code->error("Lỗi không tạo được khóa");
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

            $respone = $this->user->setPassword($data);
            if ($respone->status == 200) {

                //send email verify
                $subject = "Thay đổi mật khẩu thành công";
                $body = APP_URL . "/a/thay-doi-mat-khau/{$email}?token={$token}&type=reset-password";

                $verify = new \Thuc\Mail($this->config["mail"]["username"], $this->config["mail"]["password"], $subject, $body, $email);
                $verify->send();

                $this->code->success("Thay đổi mật khẩu thành công");
            }
        }

        if ($password != $repassword) {
            $this->code->error("Mật khẩu và mật khẩu xác nhận không giống nhau.");
        }

        $this->code->error("Bạn hãy điền đầy đủ thông tin, để thực hiện đổi mật khẩu cho bạn.");
    }

}
