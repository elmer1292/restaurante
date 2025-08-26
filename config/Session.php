<?php
class Session {

    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            $cookieParams = [
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (PHP_VERSION_ID >= 70300) {
                session_set_cookie_params($cookieParams);
            } else {
                session_set_cookie_params(
                    $cookieParams['lifetime'],
                    $cookieParams['path'],
                    $cookieParams['domain'],
                    $cookieParams['secure'],
                    $cookieParams['httponly']
                );
            }
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function destroy() {
        // Unset all session variables
        $_SESSION = [];
        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function getUserRole() {
        // Compatibilidad: buscar en ambos formatos
        if (isset($_SESSION['user_role'])) {
            return $_SESSION['user_role'];
        } elseif (isset($_SESSION['user']['Rol'])) {
            return $_SESSION['user']['Rol'];
        }
        return null;
    }

    public static function checkRole($allowedRoles) {
        if (!self::isLoggedIn()) {
            header('Location: /restaurante/login.php');
            exit();
        }

        $userRole = self::getUserRole();
        if (!in_array($userRole, $allowedRoles)) {
            header('Location: /restaurante/error.php?msg=acceso_denegado');
            exit();
        }
        return true;
    }
}