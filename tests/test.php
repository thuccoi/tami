<?php

include __DIR__ . '../../vendor/autoload.php';

$array = [1, 3, 4, 5];

echo "<pre>";
print_r(Thuc\ArrayCallback::select($array, function($e, $k) {
            return "$e $k";
        }));
