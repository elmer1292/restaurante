<?php
// imprimir_ticket_pre_factura.php: Imprime una pre-factura ESC/POS directo desde PHP
// Uso: imprimir_ticket_pre_factura.php?id_mesa=ID_Mesa

require_once __DIR__ . '/config/base_url.php';

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

// Permitir recibir el ID de mesa por GET o por argumento CLI
$idMesa = 0;
if (php_sapi_name() === 'cli') {
    foreach ($argv as $arg) {
        if (strpos($arg, 'id_mesa=') === 0) {
            $idMesa = (int)substr($arg, 8);
            break;
        }
    }
} else {
    $idMesa = isset($_GET['id_mesa']) ? (int)$_GET['id_mesa'] : 0;
}
if (!$idMesa) {
    header('Location: ' . BASE_URL . 'mesa?id_mesa=');
    exit;
}

$ventaModel = new VentaModel();
$comanda = $ventaModel->getVentaActivaByMesa($idMesa);
if (!$comanda) {
    echo '<script>
        alert("Pre-factura impresa correctamente.");
        window.location.href = "' . BASE_URL . 'mesa?id_mesa=' . $idMesa . '";
    </script>';
    exit;
}
$idVenta = $comanda['ID_Venta'];
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    header('Location: ' . BASE_URL . 'mesa?id_mesa=' . $idMesa);
    exit;
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
$configModel = new ConfigModel();
$nombreApp = $configModel->get('nombre_app') ?: 'RESTAURANTE';
$moneda = $configModel->get('moneda') ?: 'C$';
$impresora = $configModel->get('impresora_ticket') ?: $configModel->get('impresora_barra'); // Cambia la clave si usas otra

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
    '',
    '',
    $venta['Servicio'] ?? 0,
    true // Indica que es pre-factura
);

try {
    $connector = new WindowsPrintConnector($impresora);
    $printer = new Printer($connector);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text($ticketTxt . "\n");
    $printer->feed(2);
    $printer->cut();
    $printer->close();
    header('Location: ' . BASE_URL . 'mesa?id_mesa=' . $idMesa);
} catch (Exception $e) {
    echo '<script>
    alert("Error al imprimir pre-factura: ' . htmlspecialchars($e->getMessage()) . '");
    window.location.href = "' . BASE_URL . 'mesa?id_mesa=' . $idMesa . '";
</script>';
}
exit;
