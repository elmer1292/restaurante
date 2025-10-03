<?php
// ticket.php: Vista para imprimir el ticket de una venta
// Recibe ?id=ID_Venta
$configPath = dirname(__DIR__, 2) . '/models/ConfigModel.php';
if (file_exists($configPath)) require_once $configPath;
require_once dirname(__DIR__, 2) . '/models/VentaModel.php';
require_once dirname(__DIR__, 2) . '/models/MesaModel.php';
require_once dirname(__DIR__, 2) . '/models/UserModel.php';
$configModel = class_exists('ConfigModel') ? new ConfigModel() : null;
$nombreApp = $configModel ? ($configModel->get('nombre_app') ?: 'RESTAURANTE') : 'RESTAURANTE';
$moneda = $configModel ? ($configModel->get('moneda') ?: '$') : '$';
$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ventaModel = new VentaModel();
$venta = $ventaModel->getVentaById($idVenta);
if (!$venta) {
    echo '<div style="padding:2em;text-align:center;">Venta no encontrada</div>';
    exit;
}
$mesaModel = new MesaModel();
$mesa = $mesaModel->getMesaById($venta['ID_Mesa']);
$userModel = new UserModel();
$usuario = $userModel->getUserById($venta['ID_Usuario']);
$productos = $ventaModel->getProductosDeVenta($idVenta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        body {
            width: 220px;
            font-family: monospace;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .ticket {
            width: 200px;
            margin: 0 auto;
            padding: 10px 0;
        }
        .ticket-header {
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .ticket-info {
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        .ticket-table {
            width: 100%;
            font-size: 0.9em;
            border-collapse: collapse;
        }
        .ticket-table th, .ticket-table td {
            padding: 2px 0;
            text-align: left;
        }
        .ticket-table th {
            border-bottom: 1px dashed #000;
        }
        .ticket-total {
            text-align: right;
            font-size: 1em;
            font-weight: bold;
            margin-top: 8px;
        }
        .ticket-footer {
            text-align: center;
            font-size: 0.8em;
            margin-top: 10px;
        }
        @media print {
            body { width: 220px; }
            .ticket { box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        <div class="ticket-header">
            <?php echo htmlspecialchars($nombreApp); ?><br>
            Ticket de Venta
        </div>
        <div class="ticket-info">
            Fecha: <?php echo date('d/m/Y H:i', strtotime($venta['Fecha_Hora'])); ?><br>
            Mesa: <?php echo htmlspecialchars($mesa['Numero_Mesa'] ?? ''); ?><br>
            Atendió: <?php echo htmlspecialchars($usuario['nombre_completo'] ?? ''); ?>
        </div>
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Prod.</th>
                    <th>Cant</th>
                    <th>Subt.</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($productos as $prod): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['Nombre_Producto']); ?></td>
                    <td><?php echo $prod['Cantidad']; ?></td>
                    <td><?php echo $moneda . number_format($prod['Cantidad'] * $prod['Precio_Venta'], 2); ?></td>
                </tr>
                <?php $total += $prod['Cantidad'] * $prod['Precio_Venta']; endforeach; ?>
            </tbody>
        </table>
        <div class="ticket-total">
            TOTAL: <?php echo $moneda . number_format($total, 2); ?>
        </div>
        <div class="ticket-footer">
            ¡Gracias por su compra!<br>
            <span style="font-size:0.7em;">Este ticket no es factura fiscal</span>
        </div>
        <div class="no-print" style="text-align:center;margin-top:10px;">
            <button onclick="window.print()">Imprimir</button>
        </div>
    </div>
</body>
</html>
