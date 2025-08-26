<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';

class ComandaAjaxController extends BaseController {
    public function agregarProductos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMesa = isset($_POST['id_mesa']) ? (int)$_POST['id_mesa'] : null;
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
            if (!$idMesa || empty($productos)) {
                echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
                exit;
            }
            $ventaModel = new VentaModel();
            $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
            if (!$comanda) {
                // Crear nueva comanda si no existe
                // Aquí deberías tener lógica para crear la venta y obtener el ID
                echo json_encode(['success' => false, 'error' => 'No hay comanda activa']);
                exit;
            }
            $idVenta = $comanda['ID_Venta'];
            $ok = true;
            foreach ($productos as $p) {
                $res = $ventaModel->addSaleDetail($idVenta, $p['id'], $p['cantidad'], $p['precio']);
                if (!$res) $ok = false;
            }
            $ventaModel->actualizarTotal($idVenta);
            echo json_encode(['success' => $ok]);
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        exit;
    }
}
