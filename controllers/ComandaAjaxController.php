<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';

class ComandaAjaxController extends BaseController {
    public function agregarProductos() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idMesa = Validator::int(Validator::get($_POST, 'id_mesa'));
            $productos = Validator::get($_POST, 'productos');
            $productos = is_string($productos) ? json_decode($productos, true) : $productos;
            if ($idMesa === null || empty($productos)) {
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
                    $pid = Validator::int(Validator::get($p, 'id'));
                    $cantidad = Validator::int(Validator::get($p, 'cantidad'));
                    $precio = Validator::float(Validator::get($p, 'precio'));
                    $nombre = Validator::sanitizeString(Validator::get($p, 'nombre', ''));
                    if ($pid === null || $cantidad === null || $precio === null) {
                        throw new Exception('Producto inválido: ' . $nombre);
                    }
                    $res = $ventaModel->addSaleDetail($idVenta, $pid, $cantidad, $precio);
                    if (!$res) {
                        throw new Exception('Error al agregar producto: ' . $nombre);
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
