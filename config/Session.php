<?php
class Session {

    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
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