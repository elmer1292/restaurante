<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<div class="container py-4">
    <h2 class="mb-4">Cierre de Caja - Ventas Diarias</h2>
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'ok'): ?>
            <div class="alert alert-success">Ticket de cierre enviado a la impresora correctamente.</div>
        <?php elseif ($_GET['msg'] === 'error'): ?>
            <div class="alert alert-danger">Error al imprimir el ticket de cierre. Verifique la impresora.</div>
        <?php endif; ?>
    <?php endif; ?>
    <form class="row g-3 mb-4" method="get" action="<?= BASE_URL ?>reportes/cierre_caja">
        <div class="col-auto">
            <label for="fecha" class="col-form-label">Fecha:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= htmlspecialchars($fecha) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
    <?php
    // Inicializar acumuladores para métodos de pago y movimientos
    $totalesPago = [
        'Efectivo' => 0,
        'Tarjeta' => 0,
        'Transferencia' => 0,
        'Otro' => 0
    ];
    $granTotal = 0; $granServicio = 0; $granFinal = 0;
    if ($ventas && count($ventas) > 0) {
        foreach ($ventas as $venta) {
            $granTotal += $venta['Total'];
            $granServicio += $venta['Servicio'];
            $granFinal += $venta['TotalFinal'];
            // Desglose de métodos de pago por venta
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
            // Si queda algún monto sin clasificar, va a "Otro"
            if ($restante > 0.01) {
                $totalesPago['Otro'] += $restante;
            }
        }
    }

    // Desglose de movimientos
    $apertura = 0; $egresos = 0;
    if (isset($movimientos) && is_array($movimientos)) {
        foreach ($movimientos as $mov) {
            if ($mov['Tipo'] === 'Apertura') $apertura += $mov['Monto'];
            elseif ($mov['Tipo'] === 'Egreso') $egresos += $mov['Monto'];
        }
    }
    // Obtener ingresos no ventas (manuales)
    require_once dirname(__DIR__, 2) . '/models/MovimientoModel.php';
    $movModel = new MovimientoModel();
    $ingresos = $movModel->obtenerIngresosNoVentas($fecha);
    $efectivoEntregar = $apertura + $ingresos + $totalesPago['Efectivo'] - $egresos;
    ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Resumen Monetario</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Apertura de Caja</th><td class="text-end">C$ <?= number_format($apertura, 2) ?></td></tr>
                        <tr><th>Ingresos (no ventas)</th><td class="text-end">C$ <?= number_format($ingresos, 2) ?></td></tr>
                        <tr><th>Egresos</th><td class="text-end">C$ <?= number_format($egresos, 2) ?></td></tr>
                        <tr class="table-info"><th>Efectivo por Ventas</th><td class="text-end">C$ <?= number_format($totalesPago['Efectivo'], 2) ?></td></tr>
                        <tr class="table-success fw-bold"><th>Efectivo a Entregar</th><td class="text-end">C$ <?= number_format($efectivoEntregar, 2) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Ventas por Método de Pago</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Efectivo</th><td class="text-end">C$ <?= number_format($totalesPago['Efectivo'], 2) ?></td></tr>
                        <tr><th>Tarjeta</th><td class="text-end">C$ <?= number_format($totalesPago['Tarjeta'], 2) ?></td></tr>
                        <tr><th>Transferencia</th><td class="text-end">C$ <?= number_format($totalesPago['Transferencia'], 2) ?></td></tr>
                        <tr><th>Otro</th><td class="text-end">C$ <?= number_format($totalesPago['Otro'], 2) ?></td></tr>
                        <tr class="table-success fw-bold"><th>Total Ventas</th><td class="text-end">C$ <?= number_format($granFinal, 2) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Detalle de Ventas</h5>
    <?php if ($ventas && count($ventas) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th># Venta</th>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Método de Pago</th>
                    <th>Total</th>
                    <th>Servicio</th>
                    <th>Total Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td><?= (int)$venta['ID_Venta'] ?></td>
                    <td><?= htmlspecialchars($venta['Fecha_Hora']) ?></td>
                    <td><?= htmlspecialchars($venta['usuario']) ?></td>
                    <td><?= htmlspecialchars($venta['Metodo_Pago']) ?></td>
                    <td>C$ <?= number_format($venta['Total'], 2) ?></td>
                    <td>C$ <?= number_format($venta['Servicio'], 2) ?></td>
                    <td class="fw-bold">C$ <?= number_format($venta['TotalFinal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <th colspan="4" class="text-end">Totales:</th>
                    <th>C$ <?= number_format($granTotal, 2) ?></th>
                    <th>C$ <?= number_format($granServicio, 2) ?></th>
                    <th>C$ <?= number_format($granFinal, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">No hay ventas registradas para esta fecha.</div>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>reportes" class="btn btn-secondary btn-sm mt-3">Volver a reportes</a>
    <a href="<?= BASE_URL ?>imprimir_ticket_cierre.php?fecha=<?= htmlspecialchars($fecha) ?>" class="btn btn-primary btn-sm mt-3">Imprimir Ticket Cierre</a>
</div>
