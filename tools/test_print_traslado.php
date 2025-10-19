<?php
// tools/test_print_traslado.php
// Uso: php tools/test_print_traslado.php <idVenta> [--print]
require_once __DIR__ . '/../config/base_url.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/VentaModel.php';
require_once __DIR__ . '/../helpers/TicketHelper.php';
require_once __DIR__ . '/../helpers/ImpresoraHelper.php';

// Usage: php tools/test_print_traslado.php <idVenta> [origen] [destino] [--print]
$idVenta = $argv[1] ?? null;
$origen = isset($argv[2]) && is_numeric($argv[2]) ? (int)$argv[2] : 1;
$destino = isset($argv[3]) && is_numeric($argv[3]) ? (int)$argv[3] : 2;
$doPrint = in_array('--print', $argv, true);
if (!$idVenta) {
    echo "Usage: php tools/test_print_traslado.php <idVenta> [--print]\n";
    exit(1);
}
$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaConDetalles((int)$idVenta);
if (!$venta) {
    echo "Venta $idVenta no encontrada\n";
    exit(1);
}

// Generar comandas
$comandaCocina = TicketHelper::generarComandaTraslado($venta, $origen, $destino, 'cocina', 40);
$comandaBarra = TicketHelper::generarComandaTraslado($venta, $origen, $destino, 'barra', 40);

// Crear carpeta tools/prints si no existe
$printsDir = __DIR__ . '/prints';
if (!is_dir($printsDir)) mkdir($printsDir, 0755, true);

if (!empty(trim($comandaCocina))) {
    $file = $printsDir . '/traslado_' . $idVenta . '_cocina.txt';
    file_put_contents($file, $comandaCocina);
    echo "Comanda cocina generada: $file\n";
    if ($doPrint) {
        $ok = ImpresoraHelper::imprimir('impresora_cocina', $comandaCocina);
        echo 'Impresion cocina: ' . ($ok ? 'OK' : 'FALLA') . "\n";
    }
} else {
    echo "No hay items para cocina.\n";
}

if (!empty(trim($comandaBarra))) {
    $file = $printsDir . '/traslado_' . $idVenta . '_barra.txt';
    file_put_contents($file, $comandaBarra);
    echo "Comanda barra generada: $file\n";
    if ($doPrint) {
        $ok = ImpresoraHelper::imprimir('impresora_barra', $comandaBarra);
        echo 'Impresion barra: ' . ($ok ? 'OK' : 'FALLA') . "\n";
    }
} else {
    echo "No hay items para barra.\n";
}

echo "Hecho.\n";
