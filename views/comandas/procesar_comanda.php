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
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if (!$idMesa || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$ventaModel = new VentaModel();
$mesaModel = new MesaModel();

try {
    // Iniciar la venta
    $idVenta = $ventaModel->createSale(
        1,  //Cliente por defecto (C/F)),
        $idMesa,
        'Pendiente',
        Session::get('user_id')
    );

    if (!$idVenta) {
        throw new Exception('Error al crear la venta');
    }

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

    // Marcar la mesa como ocupada
    if (!$mesaModel->updateTableStatus($idMesa, 1)) {
        throw new Exception('Error al actualizar el estado de la mesa');
    }

    echo json_encode(['success' => true, 'message' => 'Comanda procesada exitosamente']);

} catch (Exception $e) {
    error_log('Error en procesar_comanda.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
// Fin del script
?>
