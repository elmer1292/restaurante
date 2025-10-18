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

// Adaptar productos al formato esperado por TicketHelper y calcular subtotal
$detalles = [];
$total = 0.0;
foreach ($productos as $prod) {
    $subtotal = (float)$prod['Cantidad'] * (float)$prod['Precio_Venta'];
    $detalles[] = [
        'cantidad' => $prod['Cantidad'],
        'nombre' => $prod['Nombre_Producto'],
        'subtotal' => $subtotal
    ];
    $total += $subtotal;
}

// Extraer el monto total pagado (puede ser varios métodos separados por coma: "Efectivo:300, Tarjeta:100")
$metodos = array_filter(array_map('trim', explode(',', $venta['Metodo_Pago'] ?? '')));
$totalPagado = 0.0;
foreach ($metodos as $metodo) {
    $partes = explode(':', $metodo);
    if (isset($partes[1])) {
        $totalPagado += floatval(trim($partes[1]));
    }
}
$servicio = isset($venta['Servicio']) ? floatval($venta['Servicio']) : 0.0;
$cambio = $totalPagado > ($total + $servicio) ? $totalPagado - ($total + $servicio) : 0.0;

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
    $cambio,
    $servicio
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        body {
            background: #fafafa;
            margin: 0;
            padding: 0;
            font-family: 'Consolas', 'Courier New', monospace;
        }
        .ticket {
            width: 270px; /* 80mm ≈ 302px at 96dpi */
            max-width: 100vw;
            margin: 2em auto 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 1.2em 0.5em;
            font-size: 13px;
            line-height: 1.25;
            white-space: pre;
            box-shadow: 0 2px 8px #0001;
            border-radius: 6px;
            overflow-x: auto;
        }
        @media print {
            body { background: #fff; }
            .ticket {
                width: 80mm !important;
                min-width: 0;
                max-width: 80mm !important;
                margin: 0;
                border: none;
                box-shadow: none;
                padding: 0;
                font-size: 13px;
            }
            .no-print { display: none; }
            @page {
                size: 80mm auto;
                margin-top: 0mm;
                margin-bottom: 0mm;
                margin-left: 0mm;
                margin-right: 0mm;
            }
        }
        @media print {
            body { background: #fff; }
            .ticket {
                width: 240px;
                margin: 0;
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .no-print { display: none; }
        }
    </style>
    </head>
    <body>
        <pre class="ticket"><?php echo htmlspecialchars($ticketTxt); ?></pre>
        <div class="no-print" style="text-align:center;margin-top:10px;">
            <button onclick="window.print()">Imprimir</button>
        </div>
    </body>
    </html>
