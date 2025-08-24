<?php

require_once '../models/UserModel.php';
require_once 'BaseController.php';

class AuthController extends BaseController {
    public function login() {
        $error = '';

        if (Session::isLoggedIn()) {
            $this->redirect('/'); // Redirigir al dashboard si ya está logueado
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($username && $password) {
                $userModel = new UserModel();
                $user = $userModel->validateUser($username);

                if ($user && $userModel->verifyPassword($password, $user['Contrasenia'])) {
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
        }
        // Cargar la vista de login
        $this->render('views/login.php', ['error' => $error]);
    }

    public function logout() {
        Session::destroy();
        $this->redirect('/login'); // Redirigir a la página de login
    }
}
