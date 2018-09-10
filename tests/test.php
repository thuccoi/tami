<?php

include __DIR__ . '../../vendor/autoload.php';


$curl = \Thuc\Curl::call("http://tiengvietmoi.thuc/langguage/create-word", ["name" => "thuc", "type" => "success"], "GET");

echo "<pre>";
var_dump($curl);
exit;
