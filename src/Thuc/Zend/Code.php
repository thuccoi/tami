<?php

namespace Thuc\Zend;

class Code {

    public $controller;
    public static $GET = "GET";
    public static $POST = "POST";

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function sanitize($name, $method = "", $base64_encode = false) {
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

        //        encode base 64
        if ($base64_encode) {
            return base64_encode(\Thuc\Langguage::purify($string, [PHP_EOL]));
        }

        return \Thuc\Langguage::purify($string);
    }

    public function arr($name) {
        $arr = $this->controller->params()->fromPost($name);
        $result = \Thuc\ArrayCallback::select($arr, function($e) {
                    //level 1
                    if (is_array($e) || is_object($e)) {
                        $ne = [];
                        foreach ($e as $k => $v) {
                            //level 2
                            if (is_array($v) || is_object($v)) {
                                $newv = [];
                                foreach ($v as $kk => $vv) {
                                    $newv[\Thuc\Langguage::purify($kk)] = \Thuc\Langguage::purify($vv);
                                }
                                $ne[\Thuc\Langguage::purify($k)] = $newv;
                            } else {
                                $ne[\Thuc\Langguage::purify($k)] = \Thuc\Langguage::purify($v);
                            }
                        }
                        $e = $ne;
                    } else {
                        $e = \Thuc\Langguage::purify($e);
                    }

                    return $e;
                });
        return $result;
    }

    public function get($name, $base64_encode = false) {
        return $this->sanitize($name, self::$GET, $base64_encode);
    }

    public function post($name, $base64_encode = false) {
        return $this->sanitize($name, self::$POST, $base64_encode);
    }

    public function param($name, $base64_encode = false) {
        return $this->sanitize($name, "", $base64_encode);
    }

    public function release($status = 405, $message = "Error!", $data = []) {
        echo json_encode([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    public function error($message = "Error!", $data = []) {
        $this->release(405, $message, $data);
    }

    public function success($message = "Success!", $data = []) {
        $this->release(200, $message, $data);
    }

    public function notfound($message = "Not Found!", $data = []) {
        $this->release(404, $message, $data);
    }

    public function forbidden($message = "Forbidden!", $data = []) {
        $this->release(403, $message, $data);
    }

}
