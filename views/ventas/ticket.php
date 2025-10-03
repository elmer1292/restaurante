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
$moneda = $configModel->get('moneda') ?: '$';
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
$usuario = $userModel->getUserById($venta['ID_Empleado']);
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
$ticketTxt = TicketHelper::generarTicketVenta(
    $nombreApp,
    $mesa['Numero_Mesa'] ?? '',
    date('d/m/Y H:i', strtotime($venta['Fecha_Hora'])),
    $detalles,
    $total,
    $usuario['nombre_completo'] ?? '',
    $venta['ID_Venta'],
    $moneda
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
