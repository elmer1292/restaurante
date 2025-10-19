<?php
require_once __DIR__ . '/../models/VentaModel.php';
require_once __DIR__ . '/../config/Session.php';
Session::init();

$idVenta = $argv[1] ?? null;
$idDestino = $argv[2] ?? null;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['user']['ID_usuario']) ? $_SESSION['user']['ID_usuario'] : null);

if (!$idVenta || !$idDestino) {
    echo "Uso: php tools/test_trasladar_mesa.php <idVenta> <idMesaDestino>\n";
    exit(1);
}

$ventaModel = new VentaModel();
$res = $ventaModel->trasladarVentaAMesa((int)$idVenta, (int)$idDestino, $userId);
if ($res['success']) {
    echo "Traslado exitoso. Origen: " . ($res['origen'] ?? 'N/A') . "\n";
} else {
    echo "Error traslado: " . ($res['error'] ?? 'Desconocido') . "\n";
}
