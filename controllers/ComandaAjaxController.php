<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';

class ComandaAjaxController extends BaseController {
    public function agregarProductos() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idMesa = isset($_POST['id_mesa']) ? (int)$_POST['id_mesa'] : null;
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
            if (!$idMesa || empty($productos)) {
                echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
                exit;
            }
            $ventaModel = new VentaModel();
            $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
            if (!$comanda) {
                echo json_encode(['success' => false, 'error' => 'No hay comanda activa']);
                exit;
            }
            $idVenta = $comanda['ID_Venta'];
            $conn = (new Database())->connect();
            try {
                $conn->beginTransaction();
                foreach ($productos as $p) {
                    $res = $ventaModel->addSaleDetail($idVenta, $p['id'], $p['cantidad'], $p['precio']);
                    if (!$res) {
                        throw new Exception('Error al agregar producto: ' . $p['nombre']);
                    }
                }
                $ventaModel->actualizarTotal($idVenta);
                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        exit;
    }
}
