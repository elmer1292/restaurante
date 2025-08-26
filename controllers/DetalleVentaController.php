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
            // Método eliminado: actualizarEstadoDetalle
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
                // Obtener cantidad actual y el id de la venta
                $conn = (new Database())->connect();
                $stmt = $conn->prepare('SELECT Cantidad, ID_Venta FROM detalle_venta WHERE ID_Detalle = ?');
                $stmt->execute([$idDetalle]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $actual = $row ? $row['Cantidad'] : null;
                $idVenta = $row ? $row['ID_Venta'] : null;
                if ($actual === null || $idVenta === null) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Detalle no encontrado']);
                    exit;
                }
                if ($cantidadEliminar !== null && $cantidadEliminar < $actual) {
                    $result = $ventaModel->actualizarCantidadDetalle($idDetalle, $actual - $cantidadEliminar);
                } else {
                    $result = $ventaModel->eliminarDetalle($idDetalle);
                }
                // Actualizar el total de la venta
                $ventaModel->actualizarTotal($idVenta);
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
