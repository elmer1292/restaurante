// LOGGING BÁSICO PARA DEPURACIÓN
$logFile = __DIR__ . '/imprimir_ticket.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Script iniciado\n", FILE_APPEND);
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
require_once __DIR__ . '/helpers/TicketHelper.php';


// Permitir recibir el ID por GET o por argumento CLI
$idVenta = 0;
if (php_sapi_name() === 'cli') {
    // Buscar argumento id=...
    foreach ($argv as $arg) {
        if (strpos($arg, 'id=') === 0) {
            $idVenta = (int)substr($arg, 3);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - ID recibido por argv: $idVenta\n", FILE_APPEND);
            break;
        }
    }
} else {
    $idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ID recibido por GET: $idVenta\n", FILE_APPEND);
}
if (!$idVenta) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: ID de venta no especificado\n", FILE_APPEND);
    die('ID de venta no especificado.');
}

$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Venta obtenida: " . ($venta ? 'OK' : 'NO ENCONTRADA') . "\n", FILE_APPEND);
if (!$venta) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: Venta no encontrada\n", FILE_APPEND);
    die('Venta no encontrada.');
}
$mesaModel = new MesaModel();
$mesa = $mesaModel->getTableById($venta['ID_Mesa']);
$userModel = new UserModel();
$usuario = $userModel->getUserById($venta['ID_Usuario']);
$productos = $ventaModel->getSaleDetails($idVenta);

// Adaptar productos al formato esperado por TicketHelper
$detalles = [];
$total = 0;
foreach ($productos as $prod) {
    $subtotal = $prod['Cantidad'] * $prod['Precio_Venta'];
    $detalles[] = [
        'cantidad' => $prod['Cantidad'],
        'nombre' => $prod['Nombre_Producto'],
        'subtotal' => $subtotal
    ];
    $total += $subtotal;
}
// Extraer el monto total pagado (puede ser varios métodos separados por coma)
$metodos = explode(',', $venta['Metodo_Pago'] ?? '');
$totalPagado = 0;
foreach ($metodos as $metodo) {
    $partes = explode(':', $metodo);
    if (isset($partes[1])) {
        $totalPagado += floatval($partes[1]);
    }
}
$cambio = $totalPagado > $total ? $totalPagado - $total : 0;
$configModel = new ConfigModel();
$nombreApp = $configModel->get('nombre_app') ?: 'RESTAURANTE';
$moneda = $configModel->get('moneda') ?: 'C$';
$impresora = $configModel->get('impresora_ticket') ?: $configModel->get('impresora_cocina'); // Cambia la clave si usas otra

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
    $venta['Metodo_Pago'] ?? 'N/A',
    $cambio ?? 0
);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Ticket generado\n", FILE_APPEND);

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Antes de imprimir en $impresora\n", FILE_APPEND);
try {
    $connector = new WindowsPrintConnector($impresora);
    $printer = new Printer($connector);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text($ticketTxt . "\n");
    $printer->feed(2);
    $printer->cut();
    $printer->close();
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Impresión exitosa\n", FILE_APPEND);
    echo 'Ticket enviado a la impresora.';
} catch (Exception $e) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR impresión: " . $e->getMessage() . "\n", FILE_APPEND);
    echo 'Error al imprimir: ' . $e->getMessage();
}
