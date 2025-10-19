<?php
require_once __DIR__ . '/../helpers/TicketHelper.php';
require_once __DIR__ . '/../models/VentaModel.php';

$idVenta = 3; // cambiar según sea necesario
$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    echo "Venta $idVenta no encontrada\n";
    exit(1);
}
$detalles = $ventaModel->getSaleDetails($idVenta);

// Preparar parámetros para el helper
$restaurante = isset($venta['Nombre_Cliente']) ? $venta['Nombre_Cliente'] : 'Restaurante';
$mesa = isset($venta['Numero_Mesa']) ? $venta['Numero_Mesa'] : ($venta['ID_Mesa'] ?? '');
$fechaHora = isset($venta['Fecha_Hora']) ? $venta['Fecha_Hora'] : date('Y-m-d H:i');
$total = isset($venta['Total']) ? (float)$venta['Total'] : 0.0;
$empleado = isset($venta['Empleado']) ? $venta['Empleado'] : (isset($venta['Nombre_Completo']) ? $venta['Nombre_Completo'] : '');
$ticketId = $idVenta;
$moneda = 'C$';

// Metodo pago viene en ventas.Metodo_Pago, y debe imprimirse tal cual
$metodoPago = isset($venta['Metodo_Pago']) ? $venta['Metodo_Pago'] : '';

// Calcular cambio: sumar importes del metodoPago (si tiene formato Metodo:cantidad, Metodo:cantidad)
$cambio = 0.0;
if (!empty($metodoPago)) {
    // Extraer números con regex (buscar montos en la cadena)
    preg_match_all('/([0-9]+(?:\.[0-9]+)?)/', $metodoPago, $m);
    $sumaPagos = 0.0;
    if (!empty($m[0])) {
        foreach ($m[0] as $num) {
            $sumaPagos += (float)$num;
        }
    }
    // El importe a cobrar debe incluir el servicio si existe
    $importeCobrar = $total;
    // Si existe campo servicio en la venta, sumarlo
    // Nota: en este script vendemos $servicio posteriormente, pero asegurémonos de usarlo si está presente
    if (isset($venta['Servicio'])) {
        $importeCobrar += (float)$venta['Servicio'];
    }
    $cambio = $sumaPagos - $importeCobrar;
}

$servicio = isset($venta['Servicio']) ? (float)$venta['Servicio'] : 0.0;

$out = TicketHelper::generarTicketVenta('Restaurante El Niño', $mesa, $fechaHora, array_map(function($d){
    return ['cantidad' => $d['Cantidad'] ?? $d['cantidad'], 'nombre' => $d['Nombre_Producto'] ?? $d['nombre'], 'subtotal' => $d['Subtotal'] ?? $d['subtotal'] ?? 0];
}, $detalles), $total, $empleado, $ticketId, $moneda, $metodoPago, $cambio, $servicio, false);

echo $out;
