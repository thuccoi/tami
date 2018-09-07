<?php

namespace Thuc\Zend;

class Code {

    public $controller;
    public static $GET = "GET";
    public static $POST = "POST";

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function sanitize($name, $method = "") {
        $string = "";
        switch ($method) {
            case self::$GET:
                $string = $this->controller->params()->fromQuery($name);
                break;

            case self::$POST:
                $string = $this->controller->params()->fromPost($name);
                break;

            default :
                $string = $this->controller->params($name);
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

    public function param($name) {
        return $this->sanitize($name);
    }

}
