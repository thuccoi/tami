<?php

namespace Thuc\API;

class Client {

    public static function login($username, $password, $client_id, $client_secret) {
        $login = \Thuc\Curl::login(API_OAUTH_URL, [
                    "grant_type" => "password",
                    "username" => $username,
                    "password" => $password
                        ], "POST", $client_id, $client_secret
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

    public static function generateToken($client_id, $client_secret) {

        $gen = \Thuc\Curl::call(API_OAUTH_URL, [
                    "grant_type" => "client_credentials",
                    "client_id" => $client_id,
                    'client_secret' => $client_secret
                        ], "POST"
        );

        if ($gen && isset($gen["access_token"])) {
            return $gen["access_token"];
        }

        return "";
    }

    public static function verifyRespone($ENV, $capcha_secret) {

        if ($ENV == 1) {
            return true;
        }

        // empty response
        $response = null;

        // check secret key
        $reCaptcha = new \Thuc\Google\ReCaptcha($capcha_secret);

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
