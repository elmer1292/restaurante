<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';

class DetalleVentaController extends BaseController {
    public function actualizarEstado() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idDetalle = Validator::int(Validator::get($_POST, 'id_detalle'));
            $estado = Validator::sanitizeString(Validator::get($_POST, 'estado'));
            if ($idDetalle !== null && $estado !== null) {
                $ventaModel = new VentaModel();
                $result = $ventaModel->actualizarEstadoDetalle($idDetalle, $estado);
                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
        exit;
    }
    public function eliminarProducto() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idDetalle = Validator::int(Validator::get($_POST, 'id_detalle'));
            $cantidadEliminar = Validator::int(Validator::get($_POST, 'cantidad'));
            if ($idDetalle !== null) {
                $ventaModel = new VentaModel();
                // Obtener cantidad actual
                $conn = (new Database())->connect();
                $stmt = $conn->prepare('SELECT Cantidad FROM detalle_venta WHERE ID_Detalle = ?');
                $stmt->execute([$idDetalle]);
                $actual = $stmt->fetchColumn();
                if ($cantidadEliminar !== null && $cantidadEliminar < $actual) {
                    $result = $ventaModel->actualizarCantidadDetalle($idDetalle, $actual - $cantidadEliminar);
                } else {
                    $result = $ventaModel->eliminarDetalle($idDetalle);
                }
                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
                exit;
                }
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
            exit;
        }
}
