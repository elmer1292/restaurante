<?php
// tools/test_print_cierre.php
// Genera un archivo de prueba del ticket de cierre para la fecha indicada (prueba local en tools/prints)
require_once __DIR__ . '/../config/autoloader.php';
require_once __DIR__ . '/../models/ReporteModel.php';
require_once __DIR__ . '/../models/MovimientoModel.php';
require_once __DIR__ . '/../helpers/TicketHelper.php';
require_once __DIR__ . '/../models/ConfigModel.php';

$date = '2025-10-19';
$model = new ReporteModel();
$ventas = $model->getCierreCajaDiario($date);
$movModel = new MovimientoModel();
$movimientos = $movModel->obtenerMovimientos(null, $date, $date);

// Calcular totales
$totalesPago = [ 'Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0, 'Otro' => 0 ];
$granTotal = 0; $granServicio = 0; $granFinal = 0;
require_once __DIR__ . '/../models/PagoModel.php';
$pagoModel = new PagoModel();
if ($ventas && count($ventas) > 0) {
    foreach ($ventas as $venta) {
        $granTotal += $venta['Total'];
        $granServicio += $venta['Servicio'];
        $granFinal += $venta['TotalFinal'];
        $pagosVenta = $pagoModel->getPagosByVenta($venta['ID_Venta']);
        $restante = (float)$venta['TotalFinal'];
        $sumaCambioRegistrado = 0.0;
        if ($pagosVenta && is_array($pagosVenta)) {
            foreach ($pagosVenta as $p) {
                $metodoRaw = trim($p['Metodo']);
                $monto = (float)$p['Monto'];
                if ((int)$p['Es_Cambio'] === 1) { $sumaCambioRegistrado += $monto; continue; }
                if (stripos($metodoRaw,'efectivo')!==false) $totalesPago['Efectivo'] += $monto;
                elseif (stripos($metodoRaw,'tarjeta')!==false||stripos($metodoRaw,'card')!==false) $totalesPago['Tarjeta'] += $monto;
                elseif (stripos($metodoRaw,'transfer')!==false||stripos($metodoRaw,'transf')!==false) $totalesPago['Transferencia'] += $monto;
                else $totalesPago['Otro'] += $monto;
                $restante -= $monto;
            }
        }
    }
}

$movModel = new MovimientoModel();
$apertura = 0; $egresos = 0;
if ($movimientos && is_array($movimientos)) {
    foreach ($movimientos as $mov) {
        if ($mov['Tipo'] === 'Apertura') $apertura += $mov['Monto'];
        elseif ($mov['Tipo'] === 'Egreso') $egresos += $mov['Monto'];
    }
}
$ingresos = $movModel->obtenerIngresosNoVentas($date);
$efectivoEntregar = $apertura + $ingresos + $totalesPago['Efectivo'] - $egresos;

$configModel = new ConfigModel();
$restaurante = $configModel->get('nombre_app') ?: 'Restaurante';
$empleado = 'Prueba';
$ticketId = 'TEST' . date('YmdHis');
$moneda = $configModel->get('moneda') ?: 'C$';

$ticket = TicketHelper::generarTicketCierreCaja(
    $restaurante,
    $date,
    $date,
    $totalesPago['Efectivo'],
    $totalesPago['Tarjeta'],
    $granTotal,
    $granServicio,
    $granFinal,
    $empleado,
    $ticketId,
    $moneda
);

// Asegurar carpeta tools/prints
$printsDir = __DIR__ . '/prints';
if (!is_dir($printsDir)) mkdir($printsDir, 0777, true);
$filename = $printsDir . '/cierre_' . str_replace('-', '', $date) . '.txt';
file_put_contents($filename, $ticket);

echo "Archivo de prueba generado: $filename\n";
