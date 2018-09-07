<?php

namespace Thuc\Controller;

class Code {

    public static $GET = "GET";
    public static $POST = "POST";

    public function sanitize($name, $method = "") {
        $string = "";
        switch ($method) {
            case self::$GET:
                $string = $_GET[$name];
                break;

            case self::$POST:
                $string = $_POST[$name];
                break;
        }

        return \Thuc\Langguage::purify($string);
    }

    public function get($name) {
        return $this->sanitize($name, self::$GET);
    }

    public function post($name) {
        return $this->sanitize($name, self::$POST);
    }

}
