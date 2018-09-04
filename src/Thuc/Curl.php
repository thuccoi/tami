<?php

namespace Thuc;

class Curl {

    public static function login($url, $data, $method, $client_id, $client_secret) {
        return static::call($url, $data, $method, '', [
                    CURLOPT_USERPWD => $client_id . ":" . $client_secret
        ]);
    }

    public static function revokeToken($url, $data) {
        return static::call($url, $data, "POST");
    }

    public static function call($url, $data, $method, $token = "", $options = []) {
        if ($url != '') {
            $json_data = null;
            if ($data) {
                $json_data = json_encode($data);
            } else if ($data === []) {
                $json_data = '{}';
            }

            try {
                $s = curl_init();
                curl_setopt($s, CURLOPT_URL, $url);
                curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($s, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);

                $arrhead = ['Content-Type: application/json'];
                if ($token) {
                    $arrhead[] = "Authorization: " . $token;
                }

                curl_setopt($s, CURLOPT_HTTPHEADER, $arrhead);

                foreach ($options as $key => $val) {
                    curl_setopt($s, $key, $val);
                }

                $result = json_decode($result = curl_exec($s), true);
                curl_close($s);

                return $result;
            } catch (Exception $e) {
                echo 'API exception: ', $e->getMessage(), "\n";
            }
        }
        return null;
    }

    public static function multi($urls, $ipserver = null) {

        $ch = [];
        foreach ($urls as $key => $url) {
            $ch[$key] = curl_init($url);
        }

        foreach ($urls as $key => $url) {

            curl_setopt($ch[$key], CURLOPT_PROXY, $ipserver); // $proxy is ip of proxy server

            curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch[$key], CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch[$key], CURLOPT_TIMEOUT, 10);

            curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
        }

        $mh = curl_multi_init();

        foreach ($urls as $key => $url) {

            curl_multi_add_handle($mh, $ch[$key]);
        }

        // execute all queries simultaneously, and continue when all are complete
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);


        foreach ($urls as $key => $url) {
            curl_multi_remove_handle($mh, $ch[$key]);
        }

        curl_multi_close($mh);

        $response = [];
        foreach ($urls as $key => $url) {
            $response[$key] = curl_multi_getcontent($ch[$key]);
        }

        foreach ($urls as $key => $url) {
            curl_close($ch[$key]);
        }

        return $response;
    }

}
