<?php

namespace Thuc\API;

class Client {

    public static $client_id;
    public static $client_secret;

    public static function login($username, $password) {
        $login = \Thuc\Curl::login(API_OAUTH_URL, [
                    "grant_type" => "password",
                    "username" => $username,
                    "password" => $password
                        ], "POST", static::$client_id, static::$client_secret
        );

        if ($login && isset($login->access_token)) {

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

}
