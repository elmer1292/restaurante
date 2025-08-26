<?php

require_once 'BaseController.php';

class EmpleadoController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de empleados
        // Ahora se usa render para incluir header, sidebar y footer.
        $this->render('views/empleados/index.php');
    }

    /**
     * Devuelve los datos de un empleado por ID (JSON).
     * @return void
     */
    public function getEmpleado() {
        require_once __DIR__ . '/../helpers/Validator.php';
        $userModel = new UserModel();
        $id = Validator::int(Validator::get($_GET, 'id'));
        if ($id !== null) {
            $empleado = $userModel->getUserById($id);
            header('Content-Type: application/json');
            echo json_encode($empleado);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }
    }

    /**
     * Actualiza los datos de un empleado (requiere CSRF y validación).
     * @return void
     */
    public function updateEmpleado() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        $userModel = new UserModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $action = Validator::sanitizeString(Validator::get($_POST, 'action'));
            if ($action === 'update') {
                $id = Validator::int(Validator::get($_POST, 'id'));
                $nombre = Validator::sanitizeString(Validator::get($_POST, 'nombre'));
                $usuario = Validator::sanitizeString(Validator::get($_POST, 'usuario'));
                $password = Validator::get($_POST, 'password', null);
                $rol = Validator::sanitizeString(Validator::get($_POST, 'rol'));
                $estado = Validator::int(Validator::get($_POST, 'estado', 1));
                if ($id === null || $nombre === null || $usuario === null || $rol === null) {
                    header('Location: /empleados?mensaje=Datos inválidos');
                    exit;
                }
                // Actualizar usuario y empleado
                $result = $userModel->updateUser($id, $usuario, $password, $rol);
                if ($result) {
                    // Actualizar estado en empleados
                    $conn = (new Database())->connect();
                    $stmt = $conn->prepare('UPDATE empleados SET Nombre_Completo = ?, Estado = ? WHERE ID_Usuario = ?');
                    $stmt->execute([$nombre, $estado, $id]);
                    // Redirigir o devolver una respuesta JSON
                    header('Location: /empleados?mensaje=Empleado actualizado correctamente');
                    exit;
                } else {
                    header('Location: /empleados?mensaje=Error al actualizar empleado');
                    exit;
                }
            }
        }
    }
}
