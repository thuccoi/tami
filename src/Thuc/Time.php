<?php

namespace Thuc;

class Time {

    public static function beginOfDay($timestamp = null) {
        if (!$timestamp) {
            $timestamp = time();
        }

        list($y, $m, $d) = explode('-', date('Y-m-d', $timestamp));
        return mktime(0, 0, 0, $m, $d, $y);
    }

    public static function endOfDay($timestamp = null) {
        if (!$timestamp) {
            $timestamp = time();
        }

        list($y, $m, $d) = explode('-', date('Y-m-d', $timestamp));
        return mktime(0, 0, 0, $m, $d + 1, $y);
    }

}
