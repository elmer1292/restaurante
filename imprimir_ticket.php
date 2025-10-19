<?php
// imprimir_ticket.php: Imprime un ticket ESC/POS directo desde PHP
// Uso: imprimir_ticket.php?id=ID_Venta


//Se necesita activar extension=intl en php.ini y recordar compartir la impresora


// Incluir clases necesarias de escpos-php
require_once __DIR__ . '\config\autoloader.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

require_once __DIR__ . '/models/ConfigModel.php';
require_once __DIR__ . '/models/VentaModel.php';
require_once __DIR__ . '/models/MesaModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PagoModel.php';
require_once __DIR__ . '/helpers/TicketHelper.php';


// Permitir recibir el ID por GET o por argumento CLI
$idVenta = 0;
if (php_sapi_name() === 'cli') {
    // Buscar argumento id=...
    foreach ($argv as $arg) {
        if (strpos($arg, 'id=') === 0) {
            $idVenta = (int)substr($arg, 3);
            break;
        }
    }
} else {
    $idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}
if (!$idVenta) {
    die('ID de venta no especificado.');
}

$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    die('Venta no encontrada.');
}
$mesaModel = new MesaModel();
$mesa = $mesaModel->getTableById($venta['ID_Mesa']);
$userModel = new UserModel();
$usuario = $userModel->getUserById($venta['ID_Usuario']);
$productos = $ventaModel->getSaleDetails($idVenta);

// Adaptar productos al formato esperado por TicketHelper
$detalles = [];
$total_calc = 0;
foreach ($productos as $prod) {
    // Preferir Subtotal persistido en detalle_venta cuando esté disponible
    $subtotal = isset($prod['Subtotal']) ? (float)$prod['Subtotal'] : ((float)$prod['Cantidad'] * (float)$prod['Precio_Venta']);
    // Capturar preparación si existe (aceptar mayúsculas/minúsculas)
    $prep = '';
    if (isset($prod['preparacion'])) $prep = $prod['preparacion'];
    if (isset($prod['Preparacion'])) $prep = $prep ?: $prod['Preparacion'];
    $detalles[] = [
        'cantidad' => $prod['Cantidad'],
        'nombre' => $prod['Nombre_Producto'],
        'subtotal' => $subtotal,
        'preparacion' => $prep
    ];
    $total_calc += $subtotal;
}
// Preferir total persistido en ventas si existe
$total = isset($venta['Total']) ? (float)$venta['Total'] : $total_calc;
// Obtener pagos desde la tabla 'pagos' si está disponible (no se realizan escrituras aquí)
$pagoModel = new PagoModel();
$pagos = $pagoModel->getPagosByVenta($idVenta);
$totalPagado = 0;
$cambio = 0;
$metodoPagoStr = $venta['Metodo_Pago'] ?? 'N/A';
$servicio = isset($venta['Servicio']) ? floatval($venta['Servicio']) : 0;
$configModel = new ConfigModel();
$nombreApp = $configModel->get('nombre_app') ?: 'RESTAURANTE';
$moneda = $configModel->get('moneda') ?: 'C$';
$impresora = $configModel->get('impresora_ticket') ?: $configModel->get('impresora_cocina'); // Cambia la clave si usas otra
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
    if (!empty($metodosArr)) $metodoPagoStr = implode(', ', $metodosArr);
    if ($cambio <= 0 && $totalPagado > ($total + $servicio)) {
        $cambio = $totalPagado - ($total + $servicio);
    }
}

// Generar el texto del ticket
$ticketTxt = TicketHelper::generarTicketVenta(
    $nombreApp,
    $mesa['Numero_Mesa'] ?? '',
    date('d/m/Y H:i', strtotime($venta['Fecha_Hora'])),
    $detalles,
    $total,
    $usuario['Nombre_Completo'] ?? '',
    $venta['ID_Venta'],
    $moneda,
    $metodoPagoStr,
    $cambio,
    $venta['Servicio'] ?? 0,
    false
);

try {
    $connector = new WindowsPrintConnector($impresora);
    $printer = new Printer($connector);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text($ticketTxt . "\n");
    $printer->feed(2);
    $printer->cut();
    $printer->pulse(); // Abre el cajón de dinero
    $printer->close();
    //redirigir a ventas
    header('Location: ventas');
    exit();
} catch (Exception $e) {
    echo 'Error al imprimir: ' . $e->getMessage();
}
