<?php
// helpers/Csrf.php
class Csrf {
    public static function generateToken() {
        if (empty($_SESSION['csrf_token']) || self::isExpired()) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public static function getToken() {
        return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : self::generateToken();
    }

    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token) && !self::isExpired();
    }

    public static function isExpired($ttl = 86400) { // 24 horas
        return isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time'] > $ttl);
    }

    public static function rotateToken() {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        self::generateToken();
    }
}
