<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';

class DetalleVentaController extends BaseController {
    public function actualizarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idDetalle = isset($_POST['id_detalle']) ? (int)$_POST['id_detalle'] : null;
            $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
            if ($idDetalle && $estado) {
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idDetalle = isset($_POST['id_detalle']) ? (int)$_POST['id_detalle'] : null;
                $cantidadEliminar = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : null;
                if ($idDetalle) {
                    $ventaModel = new VentaModel();
                    // Obtener cantidad actual
                    $conn = (new Database())->connect();
                    $stmt = $conn->prepare('SELECT Cantidad FROM detalle_venta WHERE ID_Detalle = ?');
                    $stmt->execute([$idDetalle]);
                    $actual = $stmt->fetchColumn();
                    if ($cantidadEliminar && $cantidadEliminar < $actual) {
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
