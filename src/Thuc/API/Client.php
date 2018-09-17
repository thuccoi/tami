<?php

namespace Thuc\API;

class Client {

    public static $client_id;
    public static $client_secret;
    public static $capcha_secret;

    public static function login($username, $password) {
        $login = \Thuc\Curl::login(API_OAUTH_URL, [
                    "grant_type" => "password",
                    "username" => $username,
                    "password" => $password
                        ], "POST", static::$client_id, static::$client_secret
        );

        if ($login && isset($login["access_token"])) {
            return true;
        }

        return false;
    }

    public static function revoke($token) {
        \Thuc\Curl::revokeToken(API_OAUTH_URL . '/revoke', [
            "token" => $token,
            "token_type_hint" => "access_token"
                ], "POST"
        );
    }

    public static function generateToken() {

        $gen = \Thuc\Curl::call(API_OAUTH_URL, [
                    "grant_type" => "client_credentials",
                    "client_id" => static::$client_id,
                    'client_secret' => static::$client_secret
                        ], "POST"
        );

        if ($gen && isset($gen["access_token"])) {

            return "Bearer " . $gen["access_token"];
        }

        return "";
    }

    public function verifyRespone() {

        // empty response
        $response = null;

        // check secret key
        $reCaptcha = new \Thuc\Google\ReCaptcha(static::$capcha_secret);

        // if submitted check response
        if (isset($_POST["g-recaptcha-response"])) {
            $response = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
            );
        }

        if ($response != null && $response->success) {
            return TRUE;
        }

        return FALSE;
    }

}
