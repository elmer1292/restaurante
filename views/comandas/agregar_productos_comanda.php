<?php
require_once '../../config/Session.php';
require_once '../../models/VentaModel.php';
require_once '../../models/MesaModel.php';

Session::init();
Session::checkRole(['Administrador', 'Mesero', 'Cajero']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$idMesa = isset($_POST['id_mesa']) ? (int)$_POST['id_mesa'] : 0;
$idVenta = isset($_POST['id_venta']) ? (int)$_POST['id_venta'] : 0;
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if (!$idMesa || !$idVenta || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$ventaModel = new VentaModel();
$mesaModel = new MesaModel();

// Verificar que la mesa esté ocupada
$mesa = $mesaModel->getTableById($idMesa);
if (!$mesa || $mesa['Estado'] != 1) {
    echo json_encode(['success' => false, 'message' => 'La mesa no está ocupada']);
    exit();
}

// Verificar que la venta exista y esté pendiente
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta || $venta['Estado'] != 'Pendiente') {
    echo json_encode(['success' => false, 'message' => 'La venta no existe o no está pendiente']);
    exit();
}

try {
    // Agregar los detalles de la venta
    foreach ($items as $item) {
        if (!$ventaModel->addSaleDetail(
            $idVenta,
            $item['id_producto'],
            $item['cantidad'],
            $item['precio_venta']
        )) {
            throw new Exception('Error al agregar producto a la venta');
        }
    }

    // Actualizar el total de la venta
    if (!$ventaModel->actualizarTotal($idVenta)) {
        throw new Exception('Error al actualizar el total de la venta');
    }

    echo json_encode(['success' => true, 'message' => 'Productos agregados exitosamente']);

} catch (Exception $e) {
    error_log('Error en agregar_productos_comanda.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>