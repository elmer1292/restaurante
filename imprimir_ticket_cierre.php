<?php
// imprimir_ticket_cierre.php: Imprime el ticket de cierre de caja (ventas diarias)
// Uso: imprimir_ticket_cierre.php?fecha=YYYY-MM-DD

require_once __DIR__ . '/config/autoloader.php';
require_once __DIR__ . '/models/ReporteModel.php';
require_once __DIR__ . '/models/MovimientoModel.php';
require_once __DIR__ . '/helpers/TicketHelper.php';
require_once __DIR__ . '/models/ConfigModel.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$model = new ReporteModel();
$ventas = $model->getCierreCajaDiario($fecha);
$movModel = new MovimientoModel();
$movimientos = $movModel->obtenerMovimientos(null, $fecha, $fecha);

// Calcular totales igual que en la vista
$totalesPago = [ 'Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0, 'Otro' => 0 ];
$granTotal = 0; $granServicio = 0; $granFinal = 0;
if ($ventas && count($ventas) > 0) {
    foreach ($ventas as $venta) {
        $granTotal += $venta['Total'];
        $granServicio += $venta['Servicio'];
        $granFinal += $venta['TotalFinal'];
        $mp = $venta['Metodo_Pago'];
        $restante = $venta['TotalFinal'];
        if ($mp) {
            $partes = explode(',', $mp);
            foreach ($partes as $parte) {
                $parte = trim($parte);
                if (stripos($parte, 'Efectivo:') !== false) {
                    $monto = floatval(preg_replace('/[^0-9.]/', '', substr($parte, stripos($parte, ':')+1)));
                    $totalesPago['Efectivo'] += $monto;
                    $restante -= $monto;
                } elseif (stripos($parte, 'Tarjeta:') !== false) {
                    $monto = floatval(preg_replace('/[^0-9.]/', '', substr($parte, stripos($parte, ':')+1)));
                    $totalesPago['Tarjeta'] += $monto;
                    $restante -= $monto;
                } elseif (stripos($parte, 'Transferencia:') !== false || stripos($parte, 'Transf') !== false) {
                    $monto = floatval(preg_replace('/[^0-9.]/', '', substr($parte, stripos($parte, ':')+1)));
                    $totalesPago['Transferencia'] += $monto;
                    $restante -= $monto;
                }
            }
        }
        if ($restante > 0.01) {
            $totalesPago['Otro'] += $restante;
        }
    }
}
$apertura = 0; $egresos = 0;
if (isset($movimientos) && is_array($movimientos)) {
    foreach ($movimientos as $mov) {
        if ($mov['Tipo'] === 'Apertura') $apertura += $mov['Monto'];
        elseif ($mov['Tipo'] === 'Egreso') $egresos += $mov['Monto'];
    }
}
$ingresos = $movModel->obtenerIngresosNoVentas($fecha);
$efectivoEntregar = $apertura + $ingresos + $totalesPago['Efectivo'] - $egresos;

// Datos para el ticket
$configModel = new ConfigModel();
$restaurante = $configModel->get('nombre_app') ?: 'Restaurante';
$empleado = isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuario';
$ticketId = date('Ymd') . rand(100,999);
$moneda = $configModel->get('moneda') ?: 'C$';

$ticket = TicketHelper::generarTicketCierreCaja(
    $restaurante,
    $fecha,
    $fecha,
    $totalesPago['Efectivo'],
    $totalesPago['Tarjeta'],
    $granTotal,
    $granServicio,
    $granFinal,
    $empleado,
    $ticketId,
    $moneda
);

// Imprimir usando la impresora designada para cierre
$impresora = $configModel->get('impresora_ticket');

        try {
            $connector = new WindowsPrintConnector($impresora);
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($ticket . "\n");
            $printer->feed(2);
            $printer->cut();
            $printer->close();
            $msg = 'ok';
        } catch (Exception $e) {
            $msg = 'error';
        }
    $redirectUrl = 'reportes/cierre_caja?fecha=' . urlencode($fecha) . '&msg=' . $msg;
    header('Location: ' . $redirectUrl);
    exit;
