<?php
// ticket.php: Vista para imprimir el ticket de una venta
// Recibe ?id=ID_Venta
require_once dirname(__DIR__, 2) . '/models/ConfigModel.php';
require_once dirname(__DIR__, 2) . '/models/VentaModel.php';
require_once dirname(__DIR__, 2) . '/models/MesaModel.php';
require_once dirname(__DIR__, 2) . '/models/UserModel.php';
require_once dirname(__DIR__, 2) . '/helpers/TicketHelper.php';
$configModel = new ConfigModel();
$nombreApp = $configModel->get('nombre_app') ?: 'RESTAURANTE';
$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    echo '<div style="padding:2em;text-align:center;">Venta no encontrada</div>';
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
//se extrae el valor de metodo de pago $venta['Metodo_Pago'] segun el campo Efectivo: 300, o Efectivo:1100, Tarjeta:90 son algunos ejemplos

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
$moneda = $configModel->get('moneda') ?: 'C$';
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        @media print {
            body { width: 240px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <pre class="ticket"><?php echo htmlspecialchars($ticketTxt); ?></pre>
    <div class="no-print" style="text-align:center;margin-top:10px;">
        <button onclick="window.print()">Imprimir</button>
    </div>
</body>
</html>
