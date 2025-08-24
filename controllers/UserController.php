<?php
require_once '../models/UserModel.php';

$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $empleado = $userModel->getUserById($id);
    header('Content-Type: application/json');
    echo json_encode($empleado);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            header('Location: ../views/empleados/index.php?mensaje=Empleado actualizado correctamente');
            exit;
        } else {
            header('Location: ../views/empleados/index.php?mensaje=Error al actualizar empleado');
            exit;
        }
    }
    // Puedes agregar aquí la lógica para crear o eliminar empleados
}
?>
