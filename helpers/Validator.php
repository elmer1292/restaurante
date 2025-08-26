<?php
// helpers/Validator.php
class Validator {
    public static function required($value) {
        return isset($value) && trim($value) !== '' ? $value : null;
    }
    public static function int($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : null;
    }
    public static function float($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float)$value : null;
    }
    public static function email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
    }
    public static function inArray($value, $array) {
        return in_array($value, $array) ? $value : null;
    }
    public static function maxlen($value, $max) {
        return strlen($value) <= $max ? $value : null;
    }
    public static function sanitizeString($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    public static function get($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
