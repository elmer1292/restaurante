<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    public function login() {
        $error = '';

        if (Session::isLoggedIn()) {
            $this->redirect('/'); // Redirigir al dashboard si ya está logueado
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../helpers/Validator.php';
            $username = Validator::sanitizeString(Validator::get($_POST, 'username'));
            $password = Validator::get($_POST, 'password', '');
            $csrf_token = Validator::get($_POST, 'csrf_token', '');

            // Verificar CSRF
            if (empty($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
                $error = 'Token CSRF inválido. Por favor, recargue la página.';
            } elseif ($username && $password) {
                $userModel = new UserModel();
                $user = $userModel->validateUser($username);

                if ($user && $userModel->verifyPassword($password, $user['Contrasenia'])) {
                    // Regenerar ID de sesión tras login exitoso
                    session_regenerate_id(true);
                    $empleadoId = $userModel->getEmpleadoIdByUserId($user['ID_Usuario']);
                    Session::set('user_id', $user['ID_Usuario']);
                    Session::set('empleado_id', $empleadoId);
                    Session::set('username', $user['Nombre_Usuario']);
                    Session::set('user_role', $user['Nombre_Rol']);
                    Session::set('nombre_completo', $user['Nombre_Completo']);
                    $this->redirect('/'); // Redirigir al dashboard
                } else {
                    $error = 'Usuario o contraseña incorrectos';
                }
            } else {
                $error = 'Por favor, complete todos los campos';
            }
            // Regenerar token CSRF tras cada intento
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Renderizar la vista de login siempre
        $this->render('views/login.php', ['error' => $error]);
    }

    public function logout() {
        Session::destroy();
        $this->redirect('/login'); // Redirigir a la página de login
    }
}
