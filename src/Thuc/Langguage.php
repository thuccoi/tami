<?php

namespace Thuc;

class Langguage {

    public static $charaters = [
        'a', 'á', 'à', 'ả', 'ã', 'ạ',
        'A', 'Á', 'À', 'Ả', 'Ã', 'Ạ',
        'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',
        'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',
        'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
        'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',
        'b',
        'B',
        'c',
        'C',
        'd', 'đ',
        'D', 'Đ',
        'e', 'é', 'è', 'ẻ', 'ẽ', 'ẹ',
        'E', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ',
        'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
        'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',
        'f',
        'F',
        'g',
        'G',
        'h',
        'H',
        'i', 'í', 'ì', 'ỉ', 'ĩ', 'ị',
        'I', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',
        'j',
        'J',
        'k',
        'K',
        'l',
        'L',
        'm',
        'M',
        'n',
        'N',
        'o', 'ó', 'ò', 'ỏ', 'õ', 'ọ',
        'O', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ',
        'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',
        'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ',
        'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
        'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',
        'p',
        'P',
        'q',
        'Q',
        'r',
        'R',
        's',
        'S',
        't',
        'T',
        'u', 'ú', 'ù', 'ủ', 'ũ', 'ụ',
        'U', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ',
        'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
        'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',
        'v',
        'V',
        'x',
        'X',
        'y', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
        'Y', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ',
        'w',
        'W',
        'z',
        'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        ' ', '   ', '.', ',', '/', '@', '+', '-', ':', '_',
        '‘', '’', '“', '”', '‗', '–'
    ];
    public static $vowellangguage = [
        'a', 'á', 'à', 'ả', 'ã', 'ạ',
        'A', 'Á', 'À', 'Ả', 'Ã', 'Ạ',
        'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',
        'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',
        'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
        'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',
        'e', 'é', 'è', 'ẻ', 'ẽ', 'ẹ',
        'E', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ',
        'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
        'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',
        'i', 'í', 'ì', 'ỉ', 'ĩ', 'ị',
        'I', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',
        'o', 'ó', 'ò', 'ỏ', 'õ', 'ọ',
        'O', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ',
        'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',
        'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ',
        'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
        'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',
        'u', 'ú', 'ù', 'ủ', 'ũ', 'ụ',
        'U', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ',
        'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
        'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',
        'y', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
        'Y', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'
    ];
    public static $newlangguage = [
        "Z" => ["D", "GI", "R", "Gi"],
        "z" => ["d", "gi", "r", "gI"],
        "D" => ["Đ"],
        "d" => ["đ"],
        "G" => ["GH", "Gh"],
        "g" => ["gh", "gH"],
        "F" => ["PH", "Ph"],
        "f" => ["ph", "pH"],
        "K" => ["C", "Q"],
        "k" => ["c", "q"],
        "C" => ["CH", "TR", "Ch", "Tr"],
        "c" => ["ch", "tr", "cH", "tR"],
        "Q" => ["NG", "NGH", "Ng", "NgH", "Ngh", "NGh"],
        "q" => ["ng", "ngh", "nG", "nGh", "nGH", "ngH"],
        "R" => [],
        "r" => [],
        "S" => ["X"],
        "s" => ["x"],
        "X" => ["KH", "Kh"],
        "x" => ["kh", "kH"],
        "W" => ["TH", "Th"],
        "w" => ["th", "tH"],
        "N’" => ["NH", "Nh"],
        "n’" => ["nh", "nH"]
    ];
    public static $onelangguage = [
        "Z" => ["D", "R", "Gi"],
        "z" => ["d", "gi", "r"],
        "D" => ["Đ"],
        "d" => ["đ"],
        "G" => ["Gh"],
        "g" => ["gh"],
        "F" => ["Ph"],
        "f" => ["ph"],
        "K" => ["K", "C", "Q"],
        "k" => ["k", "c", "q"],
        "C" => ["Ch", "Tr"],
        "c" => ["ch", "tr"],
        "Q" => ["Ng", "Ngh"],
        "q" => ["ng", "ngh",],
        "R" => ["R"],
        "r" => ["r"],
        "S" => ["X", "S"],
        "s" => ["x", "s"],
        "X" => ["Kh"],
        "x" => ["kh"],
        "W" => ["Th"],
        "w" => ["th"],
        "N’" => ["Nh"],
        "n’" => ["nh"]
    ];

    public static function translateVINew($string) {
        foreach (self::$newlangguage as $key => $arr) {
            if ($arr) {
                foreach ($arr as $word) {
                    $string = str_replace($word, $key, $string);
                }
            }
        }
        return $string;
    }

    public static function validChar($char) {
        foreach (self::$charaters as $val) {
            if ($char == $val) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function purify($string) {
        $string = '' . $string;

        $len = strlen($string);

        $str = "";
        for ($i = 0; $i < $len; $i++) {
            $c = mb_substr($string, $i, 1);
            if (self::validChar($c)) {
                $str = $str . $c;
            }
        }

        return $str;
    }

    public function uWord($str) {

        $str = static::purify($str);

        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return $str;
        }

        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    }

    public function isNumber($str) {
        $str = static::purify($str);

        $len = strlen($str);

        $arr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        for ($i = 0; $i < $len; $i++) {
            $c = mb_substr($str, $i, 1);
            $f = FALSE;
            foreach ($arr as $num) {
                if ($c === $num) {

                    $f = TRUE;
                    break;
                }
            }

            if ($f == FALSE) {
                return FALSE;
            }
        }

        return TRUE;
    }

}
