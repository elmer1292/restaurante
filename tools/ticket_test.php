<?php
require_once __DIR__ . '/../helpers/TicketHelper.php';
require_once __DIR__ . '/../models/VentaModel.php';
require_once __DIR__ . '/../models/PagoModel.php';

// Soporta pasar el id por CLI: php ticket_test.php id=5, o por GET si se usa en web
$idVenta = 0;
if (php_sapi_name() === 'cli') {
    foreach ($argv as $arg) {
        if (strpos($arg, 'id=') === 0) {
            $idVenta = (int)substr($arg, 3);
            break;
        }
    }
} else {
    $idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}
if (!$idVenta) $idVenta = 3; // valor por defecto si no se pasa

$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    echo "Venta $idVenta no encontrada\n";
    exit(1);
}
$detalles = $ventaModel->getSaleDetails($idVenta);

// Preparar parámetros
$restaurante = isset($venta['Nombre_Cliente']) ? $venta['Nombre_Cliente'] : 'Restaurante';
$mesa = isset($venta['Numero_Mesa']) ? $venta['Numero_Mesa'] : ($venta['ID_Mesa'] ?? '');
$fechaHora = isset($venta['Fecha_Hora']) ? $venta['Fecha_Hora'] : date('Y-m-d H:i');
$total = isset($venta['Total']) ? (float)$venta['Total'] : 0.0;
$empleado = isset($venta['Empleado']) ? $venta['Empleado'] : (isset($venta['Nombre_Completo']) ? $venta['Nombre_Completo'] : '');
$ticketId = $idVenta;
$moneda = 'C$';

// Obtener pagos desde la tabla pagos
$pagoModel = new PagoModel();
$pagos = $pagoModel->getPagosByVenta($idVenta);
$totalPagado = 0.0;
$cambio = 0.0;
$metodoPago = $venta['Metodo_Pago'] ?? '';
if ($pagos && is_array($pagos)) {
    $metodosArr = [];
    foreach ($pagos as $p) {
        if ((int)$p['Es_Cambio'] === 1) {
            $cambio += (float)$p['Monto'];
        } else {
            $totalPagado += (float)$p['Monto'];
            $metodosArr[] = $p['Metodo'] . ':' . number_format((float)$p['Monto'], 2);
        }
    }
    if (!empty($metodosArr)) $metodoPago = implode(', ', $metodosArr);
    if ($cambio <= 0 && $totalPagado > ($total + ($venta['Servicio'] ?? 0))) {
        $cambio = $totalPagado - ($total + ($venta['Servicio'] ?? 0));
    }
} else {
    // Fallback: intentar inferir cambio desde Metodo_Pago legacy
    if (!empty($metodoPago)) {
        preg_match_all('/([0-9]+(?:\.[0-9]+)?)/', $metodoPago, $m);
        $sumaPagos = 0.0;
        if (!empty($m[0])) {
            foreach ($m[0] as $num) $sumaPagos += (float)$num;
        }
        $importeCobrar = $total + ($venta['Servicio'] ?? 0);
        $cambio = $sumaPagos - $importeCobrar;
    }
}

$servicio = isset($venta['Servicio']) ? (float)$venta['Servicio'] : 0.0;

$out = TicketHelper::generarTicketVenta('Restaurante El Niño', $mesa, $fechaHora, array_map(function($d){
    return ['cantidad' => $d['Cantidad'] ?? $d['cantidad'], 'nombre' => $d['Nombre_Producto'] ?? $d['nombre'], 'subtotal' => $d['Subtotal'] ?? $d['subtotal'] ?? 0];
}, $detalles), $total, $empleado, $ticketId, $moneda, $metodoPago, $cambio, $servicio, false);

echo $out;
