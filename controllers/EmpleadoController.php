<?php

require_once 'BaseController.php';

class EmpleadoController extends BaseController {
    /**
     * Elimina (desactiva) un empleado por ID (POST).
     * @return void
     */
    public function deleteEmpleado() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        require_once __DIR__ . '/../config/Session.php';
        Session::init();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar sesión y rol
            if (!Session::isLoggedIn() || Session::getUserRole() !== 'Administrador') {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso denegado. Inicie sesión como administrador.']);
                exit;
            }
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $id = Validator::int(Validator::get($_POST, 'id'));
            $action = Validator::sanitizeString(Validator::get($_POST, 'action'));
            if (($action === 'delete' || $action === 'activar') && $id !== null) {
                $conn = (new Database())->connect();
                $nuevoEstado = 0;
                if ($action === 'activar') {
                    $nuevoEstado = 1;
                }
                try {
                    // Actualizar Estado en usuarios
                    $stmt2 = $conn->prepare('UPDATE usuarios SET Estado = ? WHERE ID_usuario = ?');
                    $stmt2->execute([$nuevoEstado, $id]);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                } catch (PDOException $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Error al eliminar/activar empleado: ' . $e->getMessage()]);
                    exit;
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'ID o acción inválida']);
                exit;
            }
        }
    }
    public function index() {
        require_once __DIR__ . '/../helpers/Pagination.php';
        $userModel = new UserModel();
        $params = getPageParams($_GET, 50);
        $empleados = $userModel->getAllUsersPaginated($params['offset'], $params['limit']);
        $loggedUserId = Session::get('user_id');
        $empleados = array_filter($empleados, function($empleado) use ($loggedUserId) {
            return $empleado['ID_usuario'] != $loggedUserId;
        });
        $totalEmpleados = $userModel->getTotalUsers();
        $totalPages = ($params['limit'] > 0) ? ceil($totalEmpleados / $params['limit']) : 1;
        $mensaje = $_GET['mensaje'] ?? '';
    $this->render('views/empleados/index.php', compact('empleados', 'totalPages', 'mensaje', 'params'));
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
        require_once __DIR__ . '/../config/Session.php';
        Session::init();
        $userModel = new UserModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar sesión y rol
            if (!Session::isLoggedIn() || Session::getUserRole() !== 'Administrador') {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Acceso denegado. Inicie sesión como administrador.']);
                exit;
            }
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $action = Validator::sanitizeString(Validator::get($_POST, 'action'));
            $id = Validator::int(Validator::get($_POST, 'id'));
            $nombre = Validator::sanitizeString(Validator::get($_POST, 'nombre'));
            $usuario = Validator::sanitizeString(Validator::get($_POST, 'usuario'));
            $password = Validator::get($_POST, 'password', null);
            $rol = Validator::sanitizeString(Validator::get($_POST, 'rol'));
            $estado = Validator::int(Validator::get($_POST, 'estado', 1));
            header('Content-Type: application/json');
            if ($action === 'create') {
                // Crear nuevo usuario y empleado
                $nombre = Validator::sanitizeString(Validator::get($_POST, 'nombre'));
                $usuario = Validator::sanitizeString(Validator::get($_POST, 'usuario'));
                $password = Validator::get($_POST, 'password', null);
                $rol = Validator::int(Validator::get($_POST, 'rol'));
                $estado = Validator::int(Validator::get($_POST, 'estado', 1));
                // Puedes agregar más campos si tu procedimiento los requiere
                $correo = Validator::sanitizeString(Validator::get($_POST, 'correo', ''));
                $telefono = Validator::sanitizeString(Validator::get($_POST, 'telefono', ''));
                $fecha_contratacion = Validator::get($_POST, 'fecha_contratacion', date('Y-m-d'));
                if (!$nombre || !$usuario || !$password || !$rol) {
                    echo json_encode(['success' => false, 'error' => 'Datos requeridos faltantes']);
                    exit;
                }
                $userData = [
                    'username' => $usuario,
                    'password' => $password,
                    'role_id' => $rol,
                    'nombre_completo' => $nombre,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'fecha_contratacion' => $fecha_contratacion
                ];
                // Si el username ya existe, retornar error amigable
                if ($userModel->checkUserExists($usuario)) {
                    echo json_encode(['success' => false, 'error' => 'El nombre de usuario ya existe']);
                    exit;
                }
                // Normalizar correo/telefono: pasar NULL a la BD si están vacíos para evitar violación de UNIQUE
                $userData['correo'] = $userData['correo'] !== '' ? $userData['correo'] : null;
                $userData['telefono'] = $userData['telefono'] !== '' ? $userData['telefono'] : null;

                $result = $userModel->createUserWithEmployee($userData);
                if ($result === true) {
                    echo json_encode(['success' => true]);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al crear empleado: ' . $result]);
                    exit;
                }
            } elseif ($action === 'update') {
                if ($id === null || $nombre === null || $usuario === null || $rol === null) {
                    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
                    exit;
                }
                $result = $userModel->updateUser($id, $usuario, $password, $rol);
                if ($result === true) {
                    $conn = (new Database())->connect();
                    // Actualizar solo Nombre_Completo en empleados
                    $stmt = $conn->prepare('UPDATE empleados SET Nombre_Completo = ? WHERE ID_Usuario = ?');
                    $stmt->execute([$nombre, $id]);
                    // Actualizar Estado en usuarios
                    $stmt2 = $conn->prepare('UPDATE usuarios SET Estado = ? WHERE ID_usuario = ?');
                    $stmt2->execute([$estado, $id]);
                    echo json_encode(['success' => true]);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al actualizar empleado: ' . $result]);
                    exit;
                }
            }
        }
    }
}
