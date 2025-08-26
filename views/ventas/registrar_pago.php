<?php
require_once '../../config/Session.php';
require_once '../../models/VentaModel.php';
Session::init();
Session::checkRole(['Administrador', 'Cajero']);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

require_once '../../helpers/Csrf.php';
$csrfToken = $_POST['csrf_token'] ?? '';
if (!Csrf::validateToken($csrfToken)) {
    echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
    exit;
}

$idVenta = isset($_POST['id_venta']) ? (int)$_POST['id_venta'] : 0;
$metodoPago = isset($_POST['metodo_pago']) ? substr(trim($_POST['metodo_pago']), 0, 20) : '';
$monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;

if (!$idVenta || !$metodoPago || $monto <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$ventaModel = new VentaModel();
$conn = (new Database())->connect();
try {
    $stmt = $conn->prepare('SELECT Total, Pagado FROM ventas WHERE ID_Venta = ?');
    $stmt->execute([$idVenta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$venta) throw new Exception('Venta no encontrada');
    $total = (float)$venta['Total'];
    $pagado = (float)$venta['Pagado'];
    $nuevoPagado = $pagado + $monto;
    if ($nuevoPagado > $total) throw new Exception('El monto pagado excede el total de la venta');
    // Actualizar venta: pagado y método de pago
    $stmtUp = $conn->prepare('UPDATE ventas SET Pagado = ?, Metodo_Pago = ? WHERE ID_Venta = ?');
    $stmtUp->execute([$nuevoPagado, $metodoPago, $idVenta]);
    // Si se pagó todo, marcar como pagada y liberar mesa
    if ($nuevoPagado >= $total) {
        $stmtEstado = $conn->prepare("UPDATE ventas SET Estado = 'Pagada' WHERE ID_Venta = ?");
        $stmtEstado->execute([$idVenta]);
        $stmtMesa = $conn->prepare("UPDATE mesas SET Estado = 0 WHERE ID_Mesa = (SELECT ID_Mesa FROM ventas WHERE ID_Venta = ?)");
        $stmtMesa->execute([$idVenta]);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
