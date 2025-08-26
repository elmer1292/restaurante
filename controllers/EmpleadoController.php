<?php

require_once 'BaseController.php';

class EmpleadoController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de empleados
        // Ahora se usa render para incluir header, sidebar y footer.
        $this->render('views/empleados/index.php');
    }

    public function getEmpleado() {
        $userModel = new UserModel();
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $empleado = $userModel->getUserById($id);
            header('Content-Type: application/json');
            echo json_encode($empleado);
            exit;
        }
    }

    public function updateEmpleado() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        $userModel = new UserModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'update') {
                $id = (int)$_POST['id'];
                $nombre = $_POST['nombre'] ?? '';
                $usuario = $_POST['usuario'] ?? '';
                $password = $_POST['password'] ?? null;
                $rol = $_POST['rol'] ?? '';
                $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

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
