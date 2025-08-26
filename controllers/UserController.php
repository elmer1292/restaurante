<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController extends BaseController {
    public function perfil() {
        Session::init();
        if (!Session::isLoggedIn()) {
            header('Location: login');
            exit;
        }
        $userId = Session::get('user_id');
        $userModel = new UserModel();
        $usuario = $userModel->getUserById($userId);
        $this->render('views/perfil.php', ['usuario' => $usuario]);
    }

    public function actualizarPerfil() {
        Session::init();
        if (!Session::isLoggedIn()) {
            header('Location: login');
            exit;
        }
        $userId = Session::get('user_id');
        $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
        if (!Csrf::validateToken($csrfToken)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
            exit;
        }
        $nombre = Validator::sanitizeString(Validator::get($_POST, 'nombre'));
        $usuario = Validator::sanitizeString(Validator::get($_POST, 'usuario'));
        $correo = Validator::sanitizeString(Validator::get($_POST, 'correo', ''));
        $telefono = Validator::sanitizeString(Validator::get($_POST, 'telefono', ''));
        $password = Validator::get($_POST, 'password', null);
        $userModel = new UserModel();
        $result = $userModel->updateUser($userId, $usuario, $password, null); // null para rol, no se cambia aquí
        $conn = (new Database())->connect();
        $stmt = $conn->prepare('UPDATE empleados SET Nombre_Completo = ?, Correo = ?, Telefono = ? WHERE ID_Usuario = ?');
        $stmt->execute([$nombre, $correo, $telefono, $userId]);
        echo json_encode(['success' => true]);
        exit;
    }
}
