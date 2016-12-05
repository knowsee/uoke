<?php
namespace Helper;

class Filter {
    private static $mixed = null;
    const INPUT_CHECK = 'Input';
    const INTVAL_CHECK = 'Intval';
    const FLOAT_CHECK = 'Float';
    const FULL_CHECK = 'FullText';
    const EMAIL_CHECK = 'Email';


    public static function Check($mixed, $checkList) {
        self::$mixed = $mixed;
        if(is_string($checkList)) $checkList = array($checkList);
        foreach($checkList as $C) {
            self::InputParse($C);
        }
        return self::$mixed;
    }

    private static function InputParse($C) {
        if(is_array(self::$mixed)) {
            self::$mixed = array_map(function($Value) use($C) {
                return self::$C($Value);
            }, self::$mixed);
        } else {
            self::$mixed = self::$C(self::$mixed);
        }
    }

    public static function Intval($string) {
        return intval($string);
    }

    public static function Float($string) {
        if(is_float($string)) {
            return $string;
        } else {
            return null;
        }
    }

    public static function Email($string) {
        return filter_var($string, FILTER_SANITIZE_EMAIL);
    }

    public static function Input($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public static function FullText($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public static function Slashes($string) {
        return addslashes($string);
    }

    public static function HtmlChars($string) {
        return htmlspecialchars($string);
    }

    public static function DeSlashes($string) {
        return stripslashes($string);
    }

    public static function DeHtmlChars($string) {
        return htmlspecialchars_decode($string);
    }

}